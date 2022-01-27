<?php

namespace Drupal\herc_quotes\Entity;

use Drupal\herc_quotes\JobquotePurchase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the jobquote item entity class.
 *
 * @ContentEntityType(
 *   id = "herc_quotes_item",
 *   label = @Translation("Jobquote item"),
 *   label_singular = @Translation("jobquote item"),
 *   label_plural = @Translation("jobquote items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count jobquote item",
 *     plural = "@count jobquote items",
 *   ),
 *   bundle_label = @Translation("Jobquote item type"),
 *   handlers = {
 *     "event" = "Drupal\herc_quotes\Event\JobquoteItemEvent",
 *     "storage" = "Drupal\herc_quotes\JobquoteItemStorage",
 *     "access" = "Drupal\herc_quotes\JobquoteItemAccessControlHandler",
 *     "permission_provider" = "Drupal\herc_quotes\JobquoteItemPermissionProvider",
 *     "list_builder" = "Drupal\herc_quotes\JobquoteItemListBuilder",
 *     "views_data" = "Drupal\herc_quotes\JobquoteItemViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\herc_quotes\Form\JobquoteItemForm",
 *       "edit" = "Drupal\herc_quotes\Form\JobquoteItemForm",
 *       "duplicate" = "Drupal\herc_quotes\Form\JobquoteItemForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "details" = "Drupal\herc_quotes\Form\JobquoteItemDetailsForm",
 *     },
 *     "local_task_provider" = {
 *       "default" = "Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\herc_quotes\JobquoteItemRouteProvider",
 *     },
 *     "inline_form" = "Drupal\herc_quotes\Form\JobquoteItemInlineForm",
 *   },
 *   base_table = "herc_quotes_item",
 *   admin_permission = "administer herc_quotes",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "jobquote_item_id",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/quotes/{herc_quotes}/items/add",
 *     "edit-form" = "/admin/commerce/quotes/{herc_quotes}/items/{herc_quotes_item}/edit",
 *     "duplicate-form" = "/admin/commerce/quotes/{herc_quotes}/items/{herc_quotes_item}/duplicate",
 *     "delete-form" = "/admin/commerce/quotes/{herc_quotes}/items/{herc_quotes_item}/delete",
 *     "collection" = "/admin/commerce/quotes/{herc_quotes}/items",
 *     "details-form" = "/quote-item/{herc_quotes_item}/details",
 *   },
 * )
 */
class JobquoteItem extends ContentEntityBase implements JobquoteItemInterface {

  use EntityChangedTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    $uri_route_parameters['herc_quotes'] = $this->getJobquoteId();
    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobquote() {
    return $this->get('jobquote_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobquoteId() {
    return $this->get('jobquote_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getPurchasableEntity() {
    return $this->get('purchasable_entity')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getPurchasableEntityId() {
    return $this->get('purchasable_entity')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->getTitle();
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    $purchasable_entity = $this->getPurchasableEntity();
    if ($purchasable_entity) {
      return $purchasable_entity->label();
    }
    else {
      return $this->t('This item is no longer available');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getQuantity() {
    return (string) $this->get('quantity')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setQuantity($quantity) {
    $this->set('quantity', (string) $quantity);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getComment() {
    return $this->get('comment')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setComment($comment) {
    $this->set('comment', $comment);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority() {
    return $this->get('priority')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPriority($priority) {
    $this->set('priority', $priority);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPurchases() {
    return $this->get('purchases')->getPurchases();
  }

  /**
   * {@inheritdoc}
   */
  public function setPurchases(array $purchases) {
    return $this->set('purchases', $purchases);
  }

  /**
   * {@inheritdoc}
   */
  public function addPurchase(JobquotePurchase $purchase) {
    $this->get('purchases')->appendItem($purchase);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removePurchase(JobquotePurchase $purchase) {
    $this->get('purchases')->removePurchase($purchase);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPurchasedQuantity() {
    $purchased_quantity = 0;
    foreach ($this->getPurchases() as $purchase) {
      $purchased_quantity += $purchase->getQuantity();
    }
    return $purchased_quantity;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastPurchasedTime() {
    $last_purchased_time = NULL;
    if ($purchases = $this->getPurchases()) {
      $purchased_times = array_map(function (JobquotePurchase $purchase) {
        return $purchase->getPurchasedTime();
      }, $purchases);
      asort($purchased_times, SORT_NUMERIC);
      $last_purchased_time = end($purchased_times);
    }
    return $last_purchased_time;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Ensure there's a reference on each jobquote.
    $jobquote = $this->getJobquote();
    if ($jobquote && !$jobquote->hasItem($this)) {
      $jobquote->addItem($this);
      $jobquote->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['type']
      ->setSetting('max_length', EntityTypeInterface::BUNDLE_MAX_LENGTH)
      ->setSetting('is_ascii', TRUE);

    // The jobquote back reference, populated by Jobquote::postSave().
    $fields['jobquote_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Jobquote'))
      ->setDescription(t('The parent jobquote.'))
      ->setSetting('target_type', 'herc_quotes')
      ->setReadOnly(TRUE);

    $fields['purchasable_entity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Purchasable entity'))
      ->setDescription(t('The purchasable entity.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -1,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    // Provide a default target_type for Views, which uses
    // base field definitions without bundle overrides.
    if (\Drupal::moduleHandler()->moduleExists('commerce_product')) {
      $fields['purchasable_entity']->setSetting('target_type', 'commerce_product_variation');
    }

    $fields['comment'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Comment'))
      ->setDescription(t('The item comment.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 25,
        'settings' => [
          'rows' => 4,
        ],
      ])
      ->setDisplayOptions('view', [
        'type' => 'string',
        'label' => 'above',
        'settings' => [],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['priority'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Priority'))
      ->setDescription(t('The item priority.'))
      ->setDefaultValue(0);

    $fields['quantity'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Quantity'))
      ->setDescription(t('The number of units.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE)
      ->setDefaultValue(1)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['purchases'] = BaseFieldDefinition::create('herc_quotes_purchase')
      ->setLabel(t('Purchases'))
      ->setDescription(t('The order ID, quantity and timestamp of each purchase.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the jobquote item was created.'))
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the jobquote item was last edited.'))
      ->setRequired(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    $purchasable_entity_type = \Drupal::entityTypeManager()->getDefinition($bundle);
    $fields = [];
    $fields['purchasable_entity'] = clone $base_field_definitions['purchasable_entity'];
    $fields['purchasable_entity']->setSetting('target_type', $purchasable_entity_type->id());
    $fields['purchasable_entity']->setLabel($purchasable_entity_type->getLabel());

    return $fields;
  }

}
