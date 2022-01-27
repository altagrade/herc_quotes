<?php

namespace Drupal\herc_quotes\Form;

use Drupal\commerce\EntityHelper;
use Drupal\herc_quotes\Entity\JobquoteType;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the jobquote settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * Constructs a new SettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   The entity display repository.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityDisplayRepositoryInterface $entity_display_repository) {
    parent::__construct($config_factory);

    $this->entityDisplayRepository = $entity_display_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'herc_quotes_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['herc_quotes.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('herc_quotes.settings');
    $form['jobquote'] = [
      '#type' => 'details',
      '#title' => 'Job Quote settings',
      '#open' => TRUE,
    ];
    $form['jobquote']['allow_multiple'] = [
      '#type' => 'checkbox',
      '#default_value' => $config->get('allow_multiple'),
      '#title' => $this->t('Allow multiple job quotes'),
      '#description' => $this->t('Determines whether multiple job quotes are allowed.'),
    ];

    $jobquote_types = JobquoteType::loadMultiple();
    $options = EntityHelper::extractLabels($jobquote_types);
    $form['jobquote']['default_type'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $config->get('default_type'),
      '#title' => $this->t('Default job quote type'),
      '#description' => $this->t('The default jobquote type to use when creating a new job quote.'),
    ];
    $form['view_modes'] = [
      '#type' => 'details',
      '#title' => 'View modes',
      '#description' => $this->t('The view mode to use when rendering job quote items.'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    foreach (herc_quotes_get_purchasable_entity_types() as $entity_type_id => $entity_type) {
      $view_modes = $this->entityDisplayRepository->getViewModes($entity_type_id);
      $view_mode_labels = array_map(function ($view_mode) {
        return $view_mode['label'];
      }, $view_modes);
      $default_view_mode = $config->get('view_modes.' . $entity_type_id);
      $default_view_mode = $default_view_mode ?: 'cart';

      $form['view_modes'][$entity_type_id] = [
        '#type' => 'select',
        '#title' => $entity_type->getLabel(),
        '#options' => $view_mode_labels,
        '#default_value' => $default_view_mode,
        '#required' => TRUE,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('herc_quotes.settings');
    $values = $form_state->getValues();
    foreach (['allow_multiple', 'default_type', 'view_modes'] as $key) {
      if (!isset($values[$key])) {
        continue;
      }
      $config->set($key, $values[$key]);
    }
    $config->save();
  }

}
