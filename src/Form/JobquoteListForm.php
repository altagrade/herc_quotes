<?php

namespace Drupal\herc_quotes\Form;

use Drupal\herc_quotes\JobquoteProviderInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the jobquote overview form.
 */
class JobquoteListForm extends FormBase {
  /**
     * The module config.
     *
     * @var \Drupal\Core\Config\ImmutableConfig
     */
  protected $config;

  /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
  protected $currentUser;

  /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
  protected $entityTypeManager;

  /**
     * The route match.
     *
     * @var \Drupal\Core\Routing\RouteMatchInterface
     */
  protected $routeMatch;

  /**
     * The jobquote provider.
     *
     * @var \Drupal\herc_quotes\JobquoteProviderInterface
     */
  protected $jobquoteProvider;

  /**
     * Constructs a new JobquoteController object.
     *
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     *   The config factory.
     * @param \Drupal\Core\Session\AccountInterface $current_user
     *   The current user.
     * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
     *   The form builder.
     * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
     *   The route match.
     * @param \Drupal\herc_quotes\JobquoteProviderInterface $jobquote_provider
     *   The jobquote provider.
     */
  public function __construct(ConfigFactoryInterface $config_factory, AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager, RouteMatchInterface $route_match, JobquoteProviderInterface $jobquote_provider) {
        $this->currentUser = $current_user;
        $this->entityTypeManager = $entity_type_manager;
        $this->routeMatch = $route_match;
        $this->jobquoteProvider = $jobquote_provider;

        $this->config = $config_factory->get('herc_quotes.settings');
      }

  /**
     * {@inheritdoc}
     */
  public static function create(ContainerInterface $container) {
        return new static(
            $container->get('config.factory'),
            $container->get('current_user'),
            $container->get('entity_type.manager'),
            $container->get('current_route_match'),
            $container->get('herc_quotes.jobquote_provider')
          );
  }

  /**
     * @inheritDoc
     */
  public function getFormId() {
        return 'commerce-jobquote-list-form';
  }

  /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
  public function buildForm(array $form, FormStateInterface $form_state) {
        $form['new'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('New job quote'),
            '#attributes' => [
                  'class' => ['container-inline']
                  ]
            ];
        $form['new']['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#size' => 60,
            '#maxlength' => 128
            ];
        $form['new']['add'] = [
            '#type' => 'submit',
            '#value' => $this->t('Add job quote'),
          ];

        $jobquotes = $this->jobquoteProvider->getJobquotes();
        if (!$jobquotes) {
            $form['message'] = [
                '#type' => 'item',
                '#markup' => $this->t('No job quote found, create your first.')
                ];

            return $form;
    }

    $form['jobquotes'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Your job quotes')
        ];
    $form['jobquotes']['items'] = [
        '#type' => 'table',
        '#header' => [
              $this->t('Name'),
              $this->t('Lines'),
              $this->t('Operations'),
              $this->t('Delete')
            ]
        ];

    foreach ($jobquotes as $i => $jobquote) {
            $form['jobquotes']['items'][$i]['name'] = [
                '#type' => 'item',
                '#markup' => $jobquote->getName()
                ];
            $form['jobquotes']['items'][$i]['lines'] = [
                '#type' => 'item',
                '#markup' => count($jobquote->getItems())
                ];
            $form['jobquotes']['items'][$i]['view'] = [
                '#title' => $this->t('view'),
                '#type' => 'link',
                '#url' => $jobquote->toUrl('user-form', ['absolute' => true]),
                '#attributes' => [
                      'class' => ['button']
                      ]
                ];
            $form['jobquotes']['items'][$i]['delete'] = [
                '#value' => $this->t('delete'),
                '#type' => 'submit',
                '#attributes' => [
                      'class' => ['button'],
                      'wl-id' => $jobquote->id()
                      ]
                ];
          }

    return $form;
  }

  /**
     * @inheritDoc
     */
  public function validateForm(array &$form, FormStateInterface $form_state) {
        if ($this->isAddTrigger($form_state) && !$form_state->getValue('name')) {
            $form_state->setErrorByName('name', $this->t('Please set a job quote name'));
            return;
    }

    if ($this->isDeleteTrigger($form_state) && ($jobquote_id = $form_state->getTriggeringElement()['#attributes']['wl-id'])) {
            $jobquotes = $this->jobquoteProvider->getJobquotesById($jobquote_id);

            if (!$jobquotes) {
                $form_state->setErrorByName('delete', $this->t('Could not load job quote for id @id', ['@id' => $jobquote_id]));
                return;
      }

      $jobquote = reset($jobquotes);
      if ($jobquote->getOwnerId() !== $this->currentUser()->id()) {
                $form_state->setErrorByName('delete', $this->t('Trying to delete a job quote you do not own.'));
                return;
      }

      $form_state->set('jobquote', $jobquote);
    }
  }

  /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @throws \Drupal\Core\Entity\EntityStorageException
     */
  public function submitForm(array &$form, FormStateInterface $form_state) {
        if ($this->isAddTrigger($form_state)) {
            $this->jobquoteProvider->createJobquote($this->config->get('default_type'), $this->currentUser, $form_state->getValue('name'));
          }

    if ($this->isDeleteTrigger($form_state)) {
            /** @var \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote */
            $jobquote = $form_state->get('jobquote');
            $jobquote->delete();
            $this->messenger()->addMessage($this->t('Job quote @name has been deleted', ['@name' => $jobquote->getName()]));
          }
  }

  private function isAddTrigger(FormStateInterface $form_state) {
        $trigger = $form_state->getTriggeringElement();
        return $trigger['#id'] === 'edit-add';
  }

  private function isDeleteTrigger(FormStateInterface $form_state) {
        $trigger = $form_state->getTriggeringElement();
        $parts = explode('-', $trigger['#id']);

        return end($parts) === 'delete';
  }
}
