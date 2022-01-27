<?php

namespace Drupal\herc_quotes;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Defines the interface for jobquote item storage.
 */
interface JobquoteItemStorageInterface extends ContentEntityStorageInterface {

  /**
   * Constructs a new jobquote item using the given purchasable entity.
   *
   * The new jobquote item isn't saved.
   *
   * @param \Drupal\commerce\PurchasableEntityInterface $entity
   *   The purchasable entity.
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteItemInterface
   *   The created jobquote item.
   */
  public function createFromPurchasableEntity(PurchasableEntityInterface $entity, array $values = []);

}
