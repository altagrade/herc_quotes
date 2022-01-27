<?php

namespace Drupal\herc_quotes\Plugin\views\field;

use Drupal\herc_quotes\JobquoteManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\Plugin\views\field\UncacheableFieldHandlerTrait;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form element for removing the jobquote item.
 *
 * @ViewsField("herc_quotes_item_remove_button")
 */
class RemoveButton extends FieldPluginBase {

  use UncacheableFieldHandlerTrait;

  /**
   * The jobquote manager.
   *
   * @var \Drupal\herc_quotes\JobquoteManagerInterface
   */
  protected $jobquoteManager;

  /**
   * Constructs a new EditRemove object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\herc_quotes\JobquoteManagerInterface $jobquote_manager
   *   The jobquote manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, JobquoteManagerInterface $jobquote_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->jobquoteManager = $jobquote_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('herc_quotes.jobquote_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function clickSortable() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $row, $field = NULL) {
    return '<!--form-item-' . $this->options['id'] . '--' . $row->index . '-->';
  }

  /**
   * Form constructor for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsForm(array &$form, FormStateInterface $form_state) {
    // Make sure we do not accidentally cache this form.
    $form['#cache']['max-age'] = 0;
    // The view is empty, abort.
    if (empty($this->view->result)) {
      unset($form['actions']);
      return;
    }

    $form[$this->options['id']]['#tree'] = TRUE;
    foreach ($this->view->result as $row_index => $row) {
      $form[$this->options['id']][$row_index] = [
        '#type' => 'submit',
        '#value' => t('Remove'),
        '#name' => 'delete-jobquote-item-' . $row_index,
        '#remove_jobquote_item' => TRUE,
        '#row_index' => $row_index,
        '#attributes' => ['class' => ['delete-jobquote-item']],
      ];
    }
  }

  /**
   * Submit handler for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsFormSubmit(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    if (!empty($triggering_element['#remove_jobquote_item'])) {
      $row_index = $triggering_element['#row_index'];
      /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
      $jobquote_item = $this->getEntity($this->view->result[$row_index]);
      $this->jobquoteManager->removeJobquoteItem($jobquote_item->getJobquote(), $jobquote_item);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing.
  }

}
