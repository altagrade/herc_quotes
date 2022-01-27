<?php

namespace Drupal\herc_quotes\Event;

use Drupal\herc_quotes\Entity\JobquoteInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the jobquote empty event.
 *
 * @see \Drupal\herc_quotes\Event\JobquoteEvents
 */
class JobquoteEmptyEvent extends Event {

  /**
   * The jobquote entity.
   *
   * @var \Drupal\herc_quotes\Entity\JobquoteInterface
   */
  protected $jobquote;

  /**
   * The removed jobquote items.
   *
   * @var \Drupal\herc_quotes\Entity\JobquoteItemInterface[]
   */
  protected $jobquoteItems;

  /**
   * Constructs a new JobquoteEmptyEvent.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote entity.
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface[] $jobquote_items
   *   The removed jobquote items.
   */
  public function __construct(JobquoteInterface $jobquote, array $jobquote_items) {
    $this->jobquote = $jobquote;
    $this->jobquoteItems = $jobquote_items;
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
   * Gets the removed jobquote items.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteItemInterface[]
   *   The removed jobquote items.
   */
  public function getItems() {
    return $this->jobquoteItems;
  }

}
