<?php

namespace Drupal\herc_quotes\Form;

use Drupal\herc_quotes\JobquoteProvider;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity\Form\EntityDuplicateFormTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the jobquote add/edit form.
 */
class JobquoteForm extends ContentEntityForm {

  use EntityDuplicateFormTrait;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The jobquote provider.
   *
   * @var \Drupal\herc_quotes\JobquoteProviderInterface
   */
  protected $jobquoteProvider;

  /**
   * Constructs a new JobquoteForm object.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   * @param \Drupal\herc_quotes\JobquoteProvider $jobquote_provider
   *   The jobquote provider.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info, TimeInterface $time, DateFormatterInterface $date_formatter, JobquoteProvider $jobquote_provider) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);

    $this->dateFormatter = $date_formatter;
    $this->jobquoteProvider = $jobquote_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('date.formatter'),
      $container->get('herc_quotes.jobquote_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\herc_quotes\Entity\Jobquote $jobquote */
    $jobquote = $this->entity;
    $form = parent::form($form, $form_state);

    $form['#tree'] = TRUE;
    // Changed must be sent to the client, for later overwrite error checking.
    $form['changed'] = [
      '#type' => 'hidden',
      '#default_value' => $jobquote->getChangedTime(),
    ];

    $last_saved = $this->dateFormatter->format($jobquote->getChangedTime(), 'short');
    $form['advanced'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['entity-meta']],
      '#weight' => 99,
    ];
    $form['meta'] = [
      '#attributes' => ['class' => ['entity-meta__header']],
      '#type' => 'container',
      '#group' => 'advanced',
      '#weight' => -100,
      'date' => NULL,
      'changed' => $this->fieldAsReadOnly($this->t('Last saved'), $last_saved),
    ];
    $form['customer'] = [
      '#type' => 'details',
      '#title' => $this->t('Customer information'),
      '#group' => 'advanced',
      '#open' => TRUE,
      '#attributes' => [
        'class' => ['jobquote-form-author'],
      ],
      '#weight' => 91,
    ];

    // Move uid/mail widgets to the sidebar, or provide read-only alternatives.
    if (isset($form['uid'])) {
      $form['uid']['#group'] = 'customer';
    }
    else {
      $user_link = $jobquote->getOwner()->toLink()->toString();
      $form['customer']['uid'] = $this->fieldAsReadOnly($this->t('Customer'), $user_link);
    }

    return $form;
  }

  /**
   * Builds a read-only form element for a field.
   *
   * @param string $label
   *   The element label.
   * @param string $value
   *   The element value.
   *
   * @return array
   *   The form element.
   */
  protected function fieldAsReadOnly($label, $value) {
    return [
      '#type' => 'item',
      '#wrapper_attributes' => [
        'class' => [Html::cleanCssIdentifier(strtolower($label)), 'container-inline'],
      ],
      '#markup' => '<h4 class="label inline">' . $label . '</h4> ' . $value,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $allow_multiple = (bool) $this->configFactory()->get('herc_quotes.settings')->get('allow_multiple');
    // If we don't allow multiple jobquotes per customer.
    if (!$allow_multiple) {
      $uid = $form_state->getValue(['uid', '0', 'target_id']);
      // If there is not uid key, there is no ability to change owner on
      // existing. But could be added a new jobquote on existing user.
      if (!empty($uid)) {
        $account = $this->entityTypeManager->getStorage('user')->load($uid);
      }
      else {
        $account = $this->currentUser();
      }
      if ($jobquote_id = $this->jobquoteProvider->getJobquoteId($this->entity->bundle(), $account)) {
        $form_state->setErrorByName('duplicate', 'Cannot create a new jobquote (Only a single jobquote per customer is allowed).');
      }
    }
    return parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);

    if ($this->entity->isNew()) {
      $actions['submit_continue'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save and add items'),
        '#continue' => TRUE,
        '#submit' => ['::submitForm', '::save'],
      ];
    }

    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    $this->messenger()->addStatus($this->t('The jobquote %label has been successfully saved.', ['%label' => $this->entity->label()]));
    if (!empty($form_state->getTriggeringElement()['#continue'])) {
      $form_state->setRedirect('entity.herc_quotes_item.collection', ['herc_quotes' => $this->entity->id()]);
    }
    else {
      $form_state->setRedirect('entity.herc_quotes.collection');
    }
  }

}
