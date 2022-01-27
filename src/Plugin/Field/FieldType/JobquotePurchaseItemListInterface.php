<?php

namespace Drupal\herc_quotes\Plugin\Field\FieldType;

use Drupal\herc_quotes\JobquotePurchase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Represents a list of jobquote purchase field items.
 */
interface JobquotePurchaseItemListInterface extends FieldItemListInterface {

  /**
   * Gets the purchase value objects from the field list.
   *
   * @return \Drupal\herc_quotes\JobquotePurchase[]
   *   The purchases.
   */
  public function getPurchases();

  /**
   * Removes the matching purchase.
   *
   * @param \Drupal\herc_quotes\JobquotePurchase $purchase
   *   The purchase.
   *
   * @return $this
   */
  public function removePurchase(JobquotePurchase $purchase);

}
