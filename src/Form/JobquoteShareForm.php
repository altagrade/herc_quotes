<?php

namespace Drupal\herc_quotes\Form;

use Drupal\herc_quotes\Mail\JobquoteShareMailInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the jobquote share form.
 */
class JobquoteShareForm extends EntityForm {

  use AjaxFormHelperTrait;

  /**
   * The jobquote share mail.
   *
   * @var \Drupal\herc_quotes\Mail\JobquoteShareMailInterface
   */
  protected $jobquoteShareMail;

  /**
   * Constructs a new JobquoteUserForm object.
   *
   * @param \Drupal\herc_quotes\Mail\JobquoteShareMailInterface $jobquote_share_mail
   *   The jobquote share mail.
   */
  public function __construct(JobquoteShareMailInterface $jobquote_share_mail) {
    $this->jobquoteShareMail = $jobquote_share_mail;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('herc_quotes.jobquote_share_mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    // Workaround for core bug #2897377.
    $form['#id'] = Html::getId($form_state->getBuildInfo()['form_id']);

    $form['to'] = [
      '#type' => 'email',
      '#title' => $this->t('Recipient'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send email'),
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
    /** @var \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote */
    $jobquote = $this->entity;
    $to = $form_state->getValue('to');
    $this->jobquoteShareMail->send($jobquote, $to);

    $this->messenger()->addStatus($this->t('Shared the job quote to @recipient.', [
      '@recipient' => $to,
    ]));
    $form_state->setRedirectUrl($jobquote->toUrl('user-form'));
  }

  /**
   * {@inheritdoc}
   */
  protected function successfulAjaxSubmit(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(new PrependCommand('.commerce-jobquote-form', ['#type' => 'status_messages']));
    $response->addCommand(new CloseDialogCommand());

    return $response;
  }

}
