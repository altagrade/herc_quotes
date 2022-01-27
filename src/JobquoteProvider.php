<?php

namespace Drupal\herc_quotes;

use Drupal\herc_quotes\Exception\DuplicateJobquoteException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Default implementation of the jobquote provider.
 */
class JobquoteProvider implements JobquoteProviderInterface {

  use StringTranslationTrait;

  /**
   * The jobquote storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $jobquoteStorage;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The session.
   *
   * @var \Drupal\herc_quotes\JobquoteSessionInterface
   */
  protected $jobquoteSession;

  /**
   * The loaded jobquote data, keyed by jobquote ID, then grouped by uid.
   *
   * @var array
   *
   * Each data item is an array with the following keys:
   * - type: The jobquote type.
   *
   * Example:
   * @code
   * 1 => [
   *   10 => ['type' => 'default'],
   * ]
   * @endcode
   */
  protected $jobquoteData = [];

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new JobquoteProvider object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\herc_quotes\JobquoteSessionInterface $jobquote_session
   *   The jobquote session.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountInterface $current_user, JobquoteSessionInterface $jobquote_session, ConfigFactoryInterface $config_factory) {
    $this->jobquoteStorage = $entity_type_manager->getStorage('herc_quotes');
    $this->currentUser = $current_user;
    $this->jobquoteSession = $jobquote_session;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function createJobquote($jobquote_type, AccountInterface $account = NULL, $name = NULL) {
    $account = $account ?: $this->currentUser;
    $uid = $account->id();

       $allow_multiple = $this->configFactory->get('herc_quotes.settings')->get('allow_multiple');
    if (empty($allow_multiple) && $this->getJobquoteId($jobquote_type, $account)) {
      // Don't allow multiple jobquote entities matching the same criteria.
      throw new DuplicateJobquoteException("A job quote for type '$jobquote_type' and account '$uid' already exists.");
    }

    // Create the new jobquote entity.
    $jobquote = $this->jobquoteStorage->create([
      'type' => $jobquote_type,
      'uid' => $uid,
      'name' => $name ?: $this->t('Job Quote'),
      'is_default' => TRUE,
    ]);
    $jobquote->save();
    // Store the new job quote id in the anonymous user's session so that it can
    // be retrieved on the next page load.
    if ($account->isAnonymous()) {
      $this->jobquoteSession->addJobquoteId($jobquote->id());
    }
    // Job quote data has already been loaded, add the new job quote to the list.
    if (isset($this->jobquoteData[$uid])) {
      $this->jobquoteData[$uid][$jobquote->id()] = [
        'type' => $jobquote_type,
      ];
    }

    return $jobquote;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobquote($jobquote_type, AccountInterface $account = NULL) {
    $jobquote = NULL;
    $jobquote_id = $this->getJobquoteId($jobquote_type, $account);
    if ($jobquote_id) {
      $jobquote = $this->jobquoteStorage->load($jobquote_id);
    }

    return $jobquote;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobquoteId($jobquote_type, AccountInterface $account = NULL) {
    $jobquote_id = NULL;
    $jobquote_data = $this->loadJobquoteData($account);
    if ($jobquote_data) {
      $search = [
        'type' => $jobquote_type,
      ];
      $jobquote_id = array_search($search, $jobquote_data);
    }

    return $jobquote_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobquotes(AccountInterface $account = NULL) {
    $jobquotes = [];
    $jobquote_ids = $this->getJobquoteIds($account);
    if ($jobquote_ids) {
      $jobquotes = $this->jobquoteStorage->loadMultiple($jobquote_ids);
    }

    return $jobquotes;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobquoteIds(AccountInterface $account = NULL) {
    $jobquote_data = $this->loadJobquoteData($account);
    return array_keys($jobquote_data);
  }

  /**
   * {@inheritDoc}
   */
  public function getJobquotesById($id) {
      if (!is_array($id)) {
          $id = [$id];
        }

    return $this->jobquoteStorage->loadMultiple($id);
  }


  /**
   * Loads the jobquote data for the given user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user. If empty, the current user is assumed.
   *
   * @return array
   *   The jobquote data.
   */
  protected function loadJobquoteData(AccountInterface $account = NULL) {
    $account = $account ?: $this->currentUser;
    $uid = $account->id();
    if (isset($this->jobquoteData[$uid])) {
      return $this->jobquoteData[$uid];
    }

    if ($account->isAuthenticated()) {
      $query = $this->jobquoteStorage->getQuery()
        ->condition('uid', $account->id())
        ->sort('is_default', 'DESC')
        ->sort('jobquote_id', 'DESC')
        ->accessCheck(FALSE);
      $jobquote_ids = $query->execute();
    }
    else {
      $jobquote_ids = $this->jobquoteSession->getJobquoteIds();
    }

    $this->jobquoteData[$uid] = [];
    if (!$jobquote_ids) {
      return [];
    }
    // Getting the jobquote data and validating the jobquote ids received from
    // the session requires loading the entities. This is a performance hit, but
    // it's assumed that these entities would be loaded at one point anyway.
    /** @var \Drupal\herc_quotes\Entity\JobquoteInterface[] $jobquotes */
    $jobquotes = $this->jobquoteStorage->loadMultiple($jobquote_ids);
    foreach ($jobquotes as $jobquote) {
      if ($jobquote->getOwnerId() != $uid) {
        // Skip jobquotes that are no longer eligible.
        continue;
      }

      $this->jobquoteData[$uid][$jobquote->id()] = [
        'type' => $jobquote->bundle(),
      ];
    }

    return $this->jobquoteData[$uid];
  }

}
