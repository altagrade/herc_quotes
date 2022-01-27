<?php

namespace Drupal\herc_quotes;

use Drupal\commerce\CommerceContentEntityStorage;
use Drupal\commerce\PurchasableEntityInterface;

/**
 * Defines the jobquote item storage.
 */
class JobquoteItemStorage extends CommerceContentEntityStorage implements JobquoteItemStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function createFromPurchasableEntity(PurchasableEntityInterface $entity, array $values = []) {
    $values += [
      'type' => $entity->getEntityTypeId(),
      'purchasable_entity' => $entity,
    ];
    return self::create($values);
  }

}
