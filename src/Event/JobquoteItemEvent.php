<?php

namespace Drupal\herc_quotes\Event;

use Drupal\herc_quotes\Entity\JobquoteItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the jobquote item event.
 *
 * @see \Drupal\herc_quotes\Event\JobquoteEvents
 */
class JobquoteItemEvent extends Event {

  /**
   * The jobquote item.
   *
   * @var \Drupal\herc_quotes\Entity\JobquoteInterface
   */
  protected $jobquoteItem;

  /**
   * Constructs a new JobquoteItemEvent object.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   */
  public function __construct(JobquoteItemInterface $jobquote_item) {
    $this->jobquoteItem = $jobquote_item;
  }

  /**
   * Gets the jobquote item.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteItemInterface
   *   Gets the jobquote item.
   */
  public function getJobquoteItem() {
    return $this->jobquoteItem;
  }

}
