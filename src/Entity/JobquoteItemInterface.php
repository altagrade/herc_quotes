<?php

namespace Drupal\herc_quotes\Entity;

use Drupal\herc_quotes\JobquotePurchase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Defines the interface for jobquote items.
 */
interface JobquoteItemInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the parent jobquote.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface|null
   *   The jobquote, or NULL.
   */
  public function getJobquote();

  /**
   * Gets the parent jobquote ID.
   *
   * @return int|null
   *   The jobquote ID, or NULL.
   */
  public function getJobquoteId();

  /**
   * Gets the purchasable entity.
   *
   * @return \Drupal\commerce\PurchasableEntityInterface|null
   *   The purchasable entity, or NULL.
   */
  public function getPurchasableEntity();

  /**
   * Gets the purchasable entity ID.
   *
   * @return int
   *   The purchasable entity ID.
   */
  public function getPurchasableEntityId();

  /**
   * Gets the jobquote item title.
   *
   * @return string
   *   The jobquote item title
   */
  public function getTitle();

  /**
   * Gets the jobquote item quantity.
   *
   * @return string
   *   The jobquote item quantity
   */
  public function getQuantity();

  /**
   * Sets the jobquote item quantity.
   *
   * @param string $quantity
   *   The jobquote item quantity.
   *
   * @return $this
   */
  public function setQuantity($quantity);

  /**
   * Gets the jobquote item comment.
   *
   * @return string
   *   The jobquote item comment.
   */
  public function getComment();

  /**
   * Sets the jobquote item comment.
   *
   * @param string $comment
   *   The jobquote item comment.
   *
   * @return $this
   */
  public function setComment($comment);

  /**
   * Gets the jobquote item priority.
   *
   * @return int
   *   The jobquote item priority.
   */
  public function getPriority();

  /**
   * Sets the jobquote item priority.
   *
   * @param int $priority
   *   The jobquote item priority.
   *
   * @return $this
   */
  public function setPriority($priority);

  /**
   * Gets the purchases.
   *
   * Each object contains the order ID, quantity, and timestamp of a purchase.
   *
   * @return \Drupal\herc_quotes\JobquotePurchase[]
   *   The purchases.
   */
  public function getPurchases();

  /**
   * Sets the purchases.
   *
   * @param \Drupal\herc_quotes\JobquotePurchase[] $purchases
   *   The purchases.
   *
   * @return $this
   */
  public function setPurchases(array $purchases);

  /**
   * Adds a new purchase.
   *
   * @param \Drupal\herc_quotes\JobquotePurchase $purchase
   *   The purchase.
   */
  public function addPurchase(JobquotePurchase $purchase);

  /**
   * Removes a purchase.
   *
   * @param \Drupal\herc_quotes\JobquotePurchase $purchase
   *   The purchase.
   *
   * @return $this
   */
  public function removePurchase(JobquotePurchase $purchase);

  /**
   * Gets the purchased quantity.
   *
   * Represents the sum of all purchase quantities.
   *
   * @return string
   *   The purchased quantity.
   */
  public function getPurchasedQuantity();

  /**
   * Gets the timestamp of the last purchase.
   *
   * @return int|null
   *   The timestamp of the last purchase, or NULL if the jobquote item
   *   hasn't been purchased yet.
   */
  public function getLastPurchasedTime();

  /**
   * Gets the jobquote item creation timestamp.
   *
   * @return int
   *   The jobquote item creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the jobquote item creation timestamp.
   *
   * @param int $timestamp
   *   The jobquote item creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

}
