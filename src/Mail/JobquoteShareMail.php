<?php

namespace Drupal\herc_quotes\Mail;

use Drupal\commerce\MailHandlerInterface;
use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the jobquote share email.
 */
class JobquoteShareMail implements JobquoteShareMailInterface {

  use StringTranslationTrait;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The commerce mail handler.
   *
   * @var \Drupal\commerce\MailHandlerInterface
   */
  protected $mailHandler;

  /**
   * Constructs a new JobquoteShareMail object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\commerce\MailHandlerInterface $mail_handler
   *   The mail handler.
   */
  public function __construct(ConfigFactoryInterface $config_factory, MailHandlerInterface $mail_handler) {
    $this->configFactory = $config_factory;
    $this->mailHandler = $mail_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function send(JobquoteInterface $jobquote, $to) {
    $owner = $jobquote->getOwner();
    if (!$owner || $owner->isAnonymous()) {
      // Only jobquotes belonging to authenticated users can be shared.
      return FALSE;
    }

    $subject = $this->t('Check out my @site-name jobquote', [
      '@site-name' => $this->configFactory->get('system.site')->get('name'),
    ]);
    $body = [
      '#theme' => 'herc_quotes_share_mail',
      '#jobquote_entity' => $jobquote,
    ];
    $params = [
      'id' => 'jobquote_share',
      'from' => $owner->getEmail(),
      'jobquote' => $jobquote,
    ];

    return $this->mailHandler->sendMail($to, $subject, $body, $params);
  }

}
