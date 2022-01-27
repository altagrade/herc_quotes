<?php

namespace Drupal\herc_quotes\Plugin\Field\FieldType;

use Drupal\herc_quotes\JobquotePurchase;
use Drupal\Core\Field\FieldItemList;

/**
 * Provides the item list class for the jobquote purchase field type.
 */
class JobquotePurchaseItemList extends FieldItemList implements JobquotePurchaseItemListInterface {

  /**
   * {@inheritdoc}
   */
  public function getPurchases() {
    $purchases = [];
    /** @var \Drupal\herc_quotes\Plugin\Field\FieldType\JobquotePurchaseItem $field_item */
    foreach ($this->list as $key => $field_item) {
      if (!$field_item->isEmpty()) {
        $purchases[$key] = $field_item->toPurchase();
      }
    }
    return $purchases;
  }

  /**
   * {@inheritdoc}
   */
  public function removePurchase(JobquotePurchase $purchase) {
    /** @var \Drupal\herc_quotes\Plugin\Field\FieldType\JobquotePurchaseItem $field_item */
    foreach ($this->list as $key => $field_item) {
      if ($purchase == $field_item->toPurchase()) {
        $this->removeItem($key);
      }
    }
  }

}
