<?php

namespace Drupal\herc_quotes\Event;

use Drupal\herc_quotes\Entity\JobquoteInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the jobquote event.
 *
 * @see \Drupal\herc_quotes\Event\JobquoteEvents
 */
class JobquoteEvent extends Event {

  /**
   * The jobquote.
   *
   * @var \Drupal\herc_quotes\Entity\JobquoteInterface
   */
  protected $jobquote;

  /**
   * Constructs a new JobquoteEvent object.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote.
   */
  public function __construct(JobquoteInterface $jobquote) {
    $this->jobquote = $jobquote;
  }

  /**
   * Gets the jobquote.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface
   *   Gets the jobquote.
   */
  public function getJobquote() {
    return $this->jobquote;
  }

}
