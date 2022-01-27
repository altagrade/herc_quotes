<?php

namespace Drupal\herc_quotes;

use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\user\UserInterface;

/**
 * Handles assigning anonymous jobquotes to user accounts.
 *
 * Invoked on login.
 */
interface JobquoteAssignmentInterface {

  /**
   * Assigns the anonymous jobquote to the given user account.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote jobquote.
   * @param \Drupal\user\UserInterface $account
   *   The user account.
   */
  public function assign(JobquoteInterface $jobquote, UserInterface $account);

  /**
   * Assigns multiple anonymous jobquotes to the given user account.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface[] $jobquotes
   *   The jobquotes.
   * @param \Drupal\user\UserInterface $account
   *   The account.
   */
  public function assignMultiple(array $jobquotes, UserInterface $account);

}
