<?php

namespace Drupal\herc_quotes;

use Drupal\herc_quotes\Event\JobquoteAssignEvent;
use Drupal\herc_quotes\Event\JobquoteEvents;
use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default jobquote assignment implementation.
 */
class JobquoteAssignment implements JobquoteAssignmentInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The jobquote manager.
   *
   * @var \Drupal\herc_quotes\JobquoteManagerInterface
   */
  protected $jobquoteManager;

  /**
   * Constructs a new JobquoteAssignment object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\herc_quotes\JobquoteManagerInterface $jobquote_manager
   *   The jobquote manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EventDispatcherInterface $event_dispatcher, ConfigFactoryInterface $config_factory, JobquoteManagerInterface $jobquote_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->eventDispatcher = $event_dispatcher;
    $this->configFactory = $config_factory;
    $this->jobquoteManager = $jobquote_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function assign(JobquoteInterface $jobquote, UserInterface $account) {
    if (!empty($jobquote->getOwnerId())) {
      // Skip jobquotes which already have an owner.
      return;
    }

    $jobquote->setOwner($account);
    // Update the referenced shipping profile.
    $shipping_profile = $jobquote->getShippingProfile();
    if ($shipping_profile && empty($shipping_profile->getOwnerId())) {
      $shipping_profile->setOwner($account);
      $shipping_profile->save();
    }
    // Notify other modules.
    $event = new JobquoteAssignEvent($jobquote, $account);
    $this->eventDispatcher->dispatch(JobquoteEvents::WISHLIST_ASSIGN, $event);

    $jobquote->save();
  }

  /**
   * {@inheritdoc}
   */
  public function assignMultiple(array $jobquotes, UserInterface $account) {
    $allow_multiple = (bool) $this->configFactory->get('herc_quotes.settings')->get('allow_multiple');
    /** @var \Drupal\herc_quotes\JobquoteStorageInterface $jobquote_storage */
    $jobquote_storage = $this->entityTypeManager->getStorage('herc_quotes');
    foreach ($jobquotes as $jobquote) {
      $default_jobquote = $jobquote_storage->loadDefaultByUser($account, $jobquote->bundle());
      // Check if multiple jobquotes are allowed, in which case we're assigning
      // the jobquote to the given account.
      if ($allow_multiple || !$default_jobquote) {
        $this->assign($jobquote, $account);
        continue;
      }
      // In case a single jobquote is allowed, we need to merge the jobquote
      // items with the default jobquote.
      $this->jobquoteManager->merge($jobquote, $default_jobquote);
    }
  }

}
