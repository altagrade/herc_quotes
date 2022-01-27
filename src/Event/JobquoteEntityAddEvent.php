<?php

namespace Drupal\herc_quotes\Event;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\herc_quotes\Entity\JobquoteItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the jobquote entity add event.
 *
 * @see \Drupal\herc_quotes\Event\JobquoteEvents
 */
class JobquoteEntityAddEvent extends Event {

  /**
   * The jobquote entity.
   *
   * @var \Drupal\herc_quotes\Entity\JobquoteInterface
   */
  protected $jobquote;

  /**
   * The added entity.
   *
   * @var \Drupal\commerce\PurchasableEntityInterface
   */
  protected $entity;

  /**
   * The quantity.
   *
   * @var float
   */
  protected $quantity;

  /**
   * The destination jobquote item.
   *
   * @var \Drupal\herc_quotes\Entity\JobquoteItemInterface
   */
  protected $jobquoteItem;

  /**
   * Constructs a new JobquoteJobquoteItemEvent.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote entity.
   * @param \Drupal\commerce\PurchasableEntityInterface $entity
   *   The added entity.
   * @param float $quantity
   *   The quantity.
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The destination jobquote item.
   */
  public function __construct(JobquoteInterface $jobquote, PurchasableEntityInterface $entity, $quantity, JobquoteItemInterface $jobquote_item) {
    $this->jobquote = $jobquote;
    $this->entity = $entity;
    $this->quantity = $quantity;
    $this->jobquoteItem = $jobquote_item;
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
   * Gets the added entity.
   *
   * @return \Drupal\commerce\PurchasableEntityInterface
   *   The added entity.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Gets the quantity.
   *
   * @return float
   *   The quantity.
   */
  public function getQuantity() {
    return $this->quantity;
  }

  /**
   * Gets the destination jobquote item.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteItemInterface
   *   The destination jobquote item.
   */
  public function getJobquoteItem() {
    return $this->jobquoteItem;
  }

}
