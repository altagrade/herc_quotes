<?php

namespace Drupal\herc_quotes\Form;

use Drupal\herc_quotes\Entity\JobquoteItemInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the jobquote details form.
 */
class JobquoteItemDetailsForm extends EntityForm {

  use AjaxFormHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
    $jobquote_item = $this->entity;

    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    // Workaround for core bug #2897377.
    $form['#id'] = Html::getId($form_state->getBuildInfo()['form_id']);

    $form['comment'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comment'),
      '#rows' => 5,
      '#default_value' => $jobquote_item->getComment(),
    ];
    $form['quantity'] = [
      '#type' => 'commerce_number',
      '#title' => $this->t('Quantity'),
      '#required' => TRUE,
      '#default_value' => $jobquote_item->getQuantity(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update details'),
      '#submit' => ['::submitForm'],
    ];
    if ($this->isAjax()) {
      $actions['submit']['#ajax']['callback'] = '::ajaxSubmit';
    }

    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
    $jobquote_item = $this->entity;
    $jobquote_item->save();

    $jobquote = $jobquote_item->getJobquote();
    $form_state->setRedirectUrl($jobquote->toUrl('user-form'));
  }

  /**
   * {@inheritdoc}
   */
  protected function successfulAjaxSubmit(array $form, FormStateInterface $form_state) {
    // We need to clear parent job quote cache, so that on refresh
    // we are able to see changes also, not only after ajax updates.
    $jobquote = $this->entity->getJobquote();
    Cache::invalidateTags($jobquote->getCacheTags());

    $response = new AjaxResponse();
    $response->addCommand(new PrependCommand('.commerce-jobquote-form', ['#type' => 'status_messages']));
    $response->addCommand(new ReplaceCommand('#jobquote-item-details-' . $this->entity->id(), [
      '#theme' => 'herc_quotes_item_details',
      '#jobquote_item_entity' => $this->entity,
    ]));
    $response->addCommand(new CloseDialogCommand());

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    assert($entity instanceof JobquoteItemInterface);
    $values = $form_state->getValues();
    unset($values['action']);
    foreach ($values as $key => $value) {
      if ($entity->hasField($key)) {
        $entity->set($key, $value);
      }
    }
  }

}
