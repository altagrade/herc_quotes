<?php

namespace Drupal\herc_quotes\Plugin\Field\FieldType;

use Drupal\herc_quotes\JobquotePurchase;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataReferenceTargetDefinition;

/**
 * Plugin implementation of the 'herc_quotes_purchase' field type.
 *
 * @FieldType(
 *   id = "herc_quotes_purchase",
 *   label = @Translation("Jobquote purchase"),
 *   description = @Translation("Stores jobquote purchases."),
 *   category = @Translation("Commerce"),
 *   list_class = "\Drupal\herc_quotes\Plugin\Field\FieldType\JobquotePurchaseItemList",
 *   default_formatter = "herc_quotes_purchase_default",
 * )
 */
class JobquotePurchaseItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $storage) {
    $properties = [];
    $properties['order_id'] = DataReferenceTargetDefinition::create('integer')
      ->setLabel(t('Order ID'))
      ->setSetting('unsigned', TRUE);
    $properties['quantity'] = DataDefinition::create('string')
      ->setLabel(t('Quantity'))
      ->setRequired(TRUE);
    $properties['purchased'] = DataDefinition::create('timestamp')
      ->setLabel(t('Purchase time'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $storage) {
    $columns = [];
    $columns['order_id'] = [
      'type' => 'int',
      'default' => 0,
      'unsigned' => TRUE,
    ];
    $columns['quantity'] = [
      'type' => 'numeric',
      'default' => 1,
      'unsigned' => TRUE,
    ];
    $columns['purchased'] = [
      'type' => 'int',
      'default' => 0,
      'unsigned' => TRUE,
    ];

    return [
      'columns' => $columns,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->order_id) || empty($this->quantity) || empty($this->purchased);
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    // Allow callers to pass a purchase value object as the field item value.
    if ($values instanceof JobquotePurchase) {
      $purchase = $values;
      $values = [
        'order_id' => $purchase->getOrderId(),
        'quantity' => $purchase->getQuantity(),
        'purchased' => $purchase->getPurchasedTime(),
      ];
    }
    parent::setValue($values, $notify);
  }

  /**
   * Gets the purchase value object for the current field item.
   *
   * @return \Drupal\herc_quotes\JobquotePurchase
   *   The purchase.
   */
  public function toPurchase() {
    return new JobquotePurchase($this->order_id, $this->quantity, $this->purchased);
  }

}
