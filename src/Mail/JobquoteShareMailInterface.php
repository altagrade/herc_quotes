<?php

namespace Drupal\herc_quotes\Mail;

use Drupal\herc_quotes\Entity\JobquoteInterface;

/**
 * Defines the interface for the jobquote share email.
 */
interface JobquoteShareMailInterface {

  /**
   * Sends the jobquote share email to the given address.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote.
   * @param string $to
   *   The address the email will be sent to.
   *
   * @return bool
   *   TRUE if the email was sent successfully, FALSE otherwise.
   */
  public function send(JobquoteInterface $jobquote, $to);

}
