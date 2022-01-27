<?php

namespace Drupal\herc_quotes;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_price\Calculator;
use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\herc_quotes\Entity\JobquoteItemInterface;
use Drupal\herc_quotes\Event\JobquoteEvents;
use Drupal\herc_quotes\Event\JobquoteEmptyEvent;
use Drupal\herc_quotes\Event\JobquoteEntityAddEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default implementation of the jobquote manager.
 *
 * Fires its own events, different from the jobquote entity events by being a
 * result of user interaction (add to jobquote form, jobquote view, etc).
 */
class JobquoteManager implements JobquoteManagerInterface {

  /**
   * The jobquote item storage.
   *
   * @var \Drupal\herc_quotes\JobquoteItemStorageInterface
   */
  protected $jobquoteItemStorage;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructs a new JobquoteManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EventDispatcherInterface $event_dispatcher) {
    $this->jobquoteItemStorage = $entity_type_manager->getStorage('herc_quotes_item');
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function emptyJobquote(JobquoteInterface $jobquote, $save_jobquote = TRUE) {
    $jobquote_items = $jobquote->getItems();
    foreach ($jobquote_items as $jobquote_item) {
      $jobquote_item->delete();
    }
    $jobquote->setItems([]);

    $this->eventDispatcher->dispatch(JobquoteEvents::WISHLIST_EMPTY, new JobquoteEmptyEvent($jobquote, $jobquote_items));
    if ($save_jobquote) {
      $jobquote->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addEntity(JobquoteInterface $jobquote, PurchasableEntityInterface $entity, $quantity = 1, $combine = TRUE, $save_jobquote = TRUE) {
    $jobquote_item = $this->jobquoteItemStorage->createFromPurchasableEntity($entity, [
      'quantity' => $quantity,
    ]);
    $purchasable_entity = $jobquote_item->getPurchasableEntity();
    $quantity = $jobquote_item->getQuantity();
    $matching_jobquote_item = NULL;
    if ($combine) {
      $matching_jobquote_item = $this->matchJobquoteItem($jobquote_item, $jobquote->getItems());
    }
    if ($matching_jobquote_item) {
      $new_quantity = Calculator::add($matching_jobquote_item->getQuantity(), $quantity);
      $matching_jobquote_item->setQuantity($new_quantity);
      $matching_jobquote_item->save();
      $saved_jobquote_item = $matching_jobquote_item;
    }
    else {
      $jobquote_item->save();
      $jobquote->addItem($jobquote_item);
      $saved_jobquote_item = $jobquote_item;
    }

    $event = new JobquoteEntityAddEvent($jobquote, $purchasable_entity, $quantity, $jobquote_item);
    $this->eventDispatcher->dispatch(JobquoteEvents::WISHLIST_ENTITY_ADD, $event);
    if ($save_jobquote) {
      $jobquote->save();
    }

    return $saved_jobquote_item;
  }

  /**
   * {@inheritdoc}
   */
  public function merge(JobquoteInterface $source, JobquoteInterface $target, $save = TRUE) {
    foreach ($source->getItems() as $jobquote_item) {
      $duplicate_jobquote_item = $jobquote_item->createDuplicate();
      $duplicate_jobquote_item->save();
      $target->addItem($duplicate_jobquote_item);
    }

    if ($save) {
      $target->save();
      $source->delete();
    }

    return $target;
  }

  /**
   * {@inheritdoc}
   */
  public function removeJobquoteItem(JobquoteInterface $jobquote, JobquoteItemInterface $jobquote_item, $save_jobquote = TRUE) {
    $jobquote->removeItem($jobquote_item);
    if ($save_jobquote) {
      $jobquote->save();
    }
    $jobquote_item->delete();
  }

  /**
   * Finds a matching jobquote item for the given one.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface[] $jobquote_items
   *   The jobquote items to match against.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteItemInterface|null
   *   A matching jobquote item, or NULL if none was found.
   */
  protected function matchJobquoteItem(JobquoteItemInterface $jobquote_item, array $jobquote_items) {
    $matching_jobquote_item = NULL;
    foreach ($jobquote_items as $existing_jobquote_item) {
      if ($existing_jobquote_item->bundle() != $jobquote_item->bundle()) {
        continue;
      }
      if ($existing_jobquote_item->getPurchasableEntityId() != $jobquote_item->getPurchasableEntityId()) {
        continue;
      }
      $matching_jobquote_item = $existing_jobquote_item;
      break;
    }

    return $matching_jobquote_item;
  }

}
