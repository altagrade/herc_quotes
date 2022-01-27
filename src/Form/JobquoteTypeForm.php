<?php

namespace Drupal\herc_quotes\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Provides an jobquote type form.
 */
class JobquoteTypeForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\herc_quotes\Entity\JobquoteTypeInterface $jobquote_type */
    $jobquote_type = $this->entity;

    $form['#tree'] = TRUE;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $jobquote_type->label(),
      '#description' => $this->t('Label for the jobquote type.'),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $jobquote_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\herc_quotes\Entity\JobquoteType::load',
        'source' => ['label'],
      ],
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
    ];

    $form['allowAnonymous'] = [
      '#type' => 'checkbox',
      '#default_value' => $jobquote_type->isAllowAnonymous(),
      '#title' => $this->t('Allow anonymous job quotes'),
    ];

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\herc_quotes\Entity\JobquoteTypeInterface $jobquote_type */
    $jobquote_type = $this->entity;
    $status = $jobquote_type->save();
    $this->messenger()->addStatus($this->t('Saved the %label jobquote type.', ['%label' => $jobquote_type->label()]));
    $form_state->setRedirect('entity.herc_quotes_type.collection');
  }

}
