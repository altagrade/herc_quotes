<?php

namespace Drupal\herc_quotes\Entity;

use Drupal\Component\Utility\Random;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Url;
use Drupal\profile\Entity\ProfileInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the jobquote entity class.
 *
 * @ContentEntityType(
 *   id = "herc_quotes",
 *   label = @Translation("Jobquote"),
 *   label_collection = @Translation("Jobquotes"),
 *   label_singular = @Translation("jobquote"),
 *   label_plural = @Translation("jobquotes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count jobquote",
 *     plural = "@count jobquotes",
 *   ),
 *   bundle_label = @Translation("Jobquote type"),
 *   handlers = {
 *     "event" = "Drupal\herc_quotes\Event\JobquoteEvent",
 *     "storage" = "Drupal\herc_quotes\JobquoteStorage",
 *     "access" = "Drupal\entity\UncacheableEntityAccessControlHandler",
 *     "query_access" = "Drupal\entity\QueryAccess\UncacheableQueryAccessHandler",
 *     "permission_provider" = "Drupal\entity\UncacheableEntityPermissionProvider",
 *     "list_builder" = "Drupal\herc_quotes\JobquoteListBuilder",
 *     "views_data" = "Drupal\commerce\CommerceEntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\herc_quotes\Form\JobquoteForm",
 *       "add" = "Drupal\herc_quotes\Form\JobquoteForm",
 *       "edit" = "Drupal\herc_quotes\Form\JobquoteForm",
 *       "duplicate" = "Drupal\herc_quotes\Form\JobquoteForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "user" = "Drupal\herc_quotes\Form\JobquoteUserForm",
 *       "share" = "Drupal\herc_quotes\Form\JobquoteShareForm",
 *     },
 *     "local_task_provider" = {
 *       "default" = "Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\herc_quotes\JobquoteRouteProvider",
 *       "delete-multiple" = "Drupal\entity\Routing\DeleteMultipleRouteProvider",
 *     },
 *   },
 *   base_table = "herc_quotes",
 *   admin_permission = "administer herc_quotes",
 *   permission_granularity = "bundle",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "jobquote_id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *     "uid" = "uid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "add-page" = "/admin/commerce/quotes/add",
 *     "add-form" = "/admin/commerce/quotes/add/{herc_quotes_type}",
 *     "edit-form" = "/admin/commerce/quotes/{herc_quotes}/edit",
 *     "duplicate-form" = "/admin/commerce/quotes/{herc_quotes}/duplicate",
 *     "delete-form" = "/admin/commerce/quotes/{herc_quotes}/delete",
 *     "delete-multiple-form" = "/admin/commerce/quotes/delete",
 *     "collection" = "/admin/commerce/quotes",
 *     "user-form" = "/user/{user}/quotes/{code}",
 *     "share-form" = "/user/{user}/quotes/{code}/share",
 *   },
 *   bundle_entity_type = "herc_quotes_type",
 *   field_ui_base_route = "entity.herc_quotes_type.edit_form"
 * )
 */
class Jobquote extends ContentEntityBase implements JobquoteInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function createDuplicate() {
    $duplicate = parent::createDuplicate();
    // Unique code cannot be transferred because their codes are unique.
    $duplicate->set('code', NULL);
    // We don't duplicate jobquote items.
    $duplicate->set('jobquote_items', []);

    return $duplicate;
  }

  /**
   * {@inheritdoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    // Can't declare "canonical" as a link template because it requires a
    // custom parameter, which breaks contribs that don't expect it.
    // StringFormatter assumes 'revision' is always a valid link template.
    if (in_array($rel, ['canonical', 'revision'])) {
      $route_name = 'entity.herc_quotes.canonical';
      $route_parameters = [
        'code' => $this->getCode(),
      ];
      $options += [
        'entity_type' => 'herc_quotes',
        'entity' => $this,
        // Display links by default based on the current language.
        'language' => $this->language(),
      ];
      return new Url($route_name, $route_parameters, $options);
    }
    else {
      return parent::toUrl($rel, $options);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    if (in_array($rel, ['user-form', 'share-form'])) {
      return [
        'user' => $this->getOwnerId(),
        'code' => $this->getCode(),
      ];
    }
    else {
      return parent::urlRouteParameters($rel);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCode() {
    return $this->get('code')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCode($code) {
    $this->set('code', $code);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getShippingProfile() {
    return $this->get('shipping_profile')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setShippingProfile(ProfileInterface $profile) {
    $this->set('shipping_profile', $profile);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getItems() {
    return $this->get('jobquote_items')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function setItems(array $jobquote_items) {
    $this->set('jobquote_items', $jobquote_items);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasItems() {
    return !$this->get('jobquote_items')->isEmpty();
  }

  /**
   * {@inheritdoc}
   */
  public function addItem(JobquoteItemInterface $jobquote_item) {
    if (!$this->hasItem($jobquote_item)) {
      $this->get('jobquote_items')->appendItem($jobquote_item);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeItem(JobquoteItemInterface $jobquote_item) {
    $index = $this->getItemIndex($jobquote_item);
    if ($index !== FALSE) {
      $this->get('jobquote_items')->offsetUnset($index);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasItem(JobquoteItemInterface $jobquote_item) {
    return $this->getItemIndex($jobquote_item) !== FALSE;
  }

  /**
   * Gets the index of the given jobquote item.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   *
   * @return int|bool
   *   The index of the given jobquote item, or FALSE if not found.
   */
  protected function getItemIndex(JobquoteItemInterface $jobquote_item) {
    $values = $this->get('jobquote_items')->getValue();
    $jobquote_item_ids = array_map(function ($value) {
      return $value['target_id'];
    }, $values);

    return array_search($jobquote_item->id(), $jobquote_item_ids);
  }

  /**
   * {@inheritdoc}
   */
  public function isDefault() {
    return (bool) $this->get('is_default')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDefault($default) {
    $this->set('is_default', (bool) $default);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublic() {
    return (bool) $this->get('is_public')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPublic($public) {
    $this->set('is_public', (bool) $public);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeepPurchasedItems() {
    return (bool) $this->get('keep_purchased_items')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setKeepPurchasedItems($keep_purchased_items) {
    $this->set('keep_purchased_items', (bool) $keep_purchased_items);
    return $this;
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
  public function preSave(EntityStorageInterface $storage) {
    /** @var \Drupal\herc_quotes\JobquoteStorageInterface $storage */
    parent::preSave($storage);

    if ($this->get('code')->isEmpty()) {
      /** @var \Drupal\herc_quotes\JobquoteStorageInterface $storage */
      $storage = $this->entityTypeManager()->getStorage('herc_quotes');
      $random = new Random();
      $code = $random->word(13);
      // Ensure code uniqueness. Collisions are rare, but possible.
      while ($storage->loadByCode($code)) {
        $code = $random->word(13);
      }
      $this->setCode($random->word(13));
    }
    // Mark the jobquote as default if there's no other default.
    if ($this->getOwnerId() && !$this->isDefault()) {
      $jobquote = $storage->loadDefaultByUser($this->getOwner(), $this->bundle());
      if (!$jobquote || !$jobquote->isDefault()) {
        $this->setDefault(TRUE);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    /** @var \Drupal\herc_quotes\JobquoteStorageInterface $storage */
    parent::postSave($storage, $update);

    // Ensure there's a back-reference on each jobquote item.
    foreach ($this->getItems() as $jobquote_item) {
      if ($jobquote_item->jobquote_id->isEmpty()) {
        $jobquote_item->jobquote_id = $this->id();
        $jobquote_item->save();
      }
    }

    if ($this->getOwnerId()) {
      $default = $this->isDefault();
      $original_default = $this->original ? $this->original->isDefault() : FALSE;
      if ($default && !$original_default) {
        $jobquotes = $storage->loadMultipleByUser($this->getOwner(), $this->bundle());
        foreach ($jobquotes as $jobquote) {
          if ($jobquote->id() != $this->id() && $jobquote->isDefault()) {
            $jobquote->setDefault(FALSE);
            $jobquote->save();
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Delete the jobquote items of a deleted jobquote.
    $jobquote_items = [];
    /** @var \Drupal\herc_quotes\Entity\JobquoteInterface $entity */
    foreach ($entities as $entity) {
      foreach ($entity->getItems() as $jobquote_item) {
        $jobquote_items[$jobquote_item->id()] = $jobquote_item;
      }
    }
    /** @var \Drupal\herc_quotes\JobquoteItemStorageInterface $jobquote_item_storage */
    $jobquote_item_storage = \Drupal::service('entity_type.manager')->getStorage('herc_quotes_item');
    $jobquote_item_storage->delete($jobquote_items);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setDescription(t('The jobquote code.'))
      ->setSetting('max_length', 25)
      ->addConstraint('UniqueField', []);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The jobquote name.'))
      ->setRequired(TRUE)
      ->setDefaultValue('')
      ->setSetting('max_length', 255)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid']->setLabel(t('Owner'))
      ->setDescription(t('The jobquote owner.'))
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['shipping_profile'] = BaseFieldDefinition::create('entity_reference_revisions')
      ->setLabel(t('Shipping profile'))
      ->setDescription(t('Shipping profile'))
      ->setSetting('target_type', 'profile')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', ['target_bundles' => ['customer']])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
        'settings' => [],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['jobquote_items'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Jobquote items'))
      ->setDescription(t('The jobquote items.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'herc_quotes_item')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'type' => 'herc_quotes_item_table',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['is_default'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Default'))
      ->setDescription(t('A boolean indicating whether the jobquote is the default one.'));

    $fields['is_public'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Public'))
      ->setDescription(t('Whether the jobquote is public.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 19,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['keep_purchased_items'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Keep purchased items in the list'))
      ->setDescription(t('Whether items should remain in the jobquote once purchased.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the jobquote was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the jobquote was last edited.'));

    return $fields;
  }

}
