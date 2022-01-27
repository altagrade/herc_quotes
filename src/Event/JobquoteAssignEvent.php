<?php

namespace Drupal\herc_quotes\Event;

use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\user\UserInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the jobquote assign event.
 *
 * @see \Drupal\herc_quotes\Event\JobquoteEvents
 */
class JobquoteAssignEvent extends Event {

  /**
   * The jobquote entity.
   *
   * @var \Drupal\herc_quotes\Entity\JobquoteInterface
   */
  protected $jobquote;

  /**
   * The user account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * Constructs a new JobquoteAssignEvent.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote entity.
   * @param \Drupal\user\UserInterface $account
   *   The user account.
   */
  public function __construct(JobquoteInterface $jobquote, UserInterface $account) {
    $this->jobquote = $jobquote;
    $this->account = $account;
  }

  /**
   * Gets the jobquote entity.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface
   *   The jobquote entity.
   */
  public function getJobquote() {
    return $this->jobquote;
  }

  /**
   * Gets the user account.
   *
   * @return \Drupal\user\UserInterface
   *   The user account.
   */
  public function getAccount() {
    return $this->account;
  }

}
