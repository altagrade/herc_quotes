<?php

namespace Drupal\herc_quotes\Event;

/**
 * Defines events for the jobquote module.
 */
final class JobquoteEvents {

  /**
   * Name of the event fired after assigning the anonymous jobquote to a user.
   *
   * Fired before the jobquote is saved.
   *
   * Use this event to implement logic such as canceling any existing jobquotes
   * the user might already have prior to the anonymous jobquote assignment.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteAssignEvent
   */
  const WISHLIST_ASSIGN = 'herc_quotes.jobquote.assign';

  /**
   * Name of the event fired after emptying the jobquote.
   *
   * Fired before the jobquote is saved.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEmptyEvent
   */
  const WISHLIST_EMPTY = 'herc_quotes.jobquote.empty';

  /**
   * Name of the event fired after adding a purchasable entity to the jobquote.
   *
   * Fired before the jobquote is saved.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEntityAddEvent
   */
  const WISHLIST_ENTITY_ADD = 'herc_quotes.entity.add';

  /**
   * Name of the event fired after loading a jobquote.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEvent
   */
  const WISHLIST_LOAD = 'herc_quotes.herc_quotes.load';

  /**
   * Name of the event fired after creating a new jobquote.
   *
   * Fired before the jobquote is saved.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEvent
   */
  const WISHLIST_CREATE = 'herc_quotes.herc_quotes.create';

  /**
   * Name of the event fired before saving a jobquote.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEvent
   */
  const WISHLIST_PRESAVE = 'herc_quotes.herc_quotes.presave';

  /**
   * Name of the event fired after saving a new jobquote.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEvent
   */
  const WISHLIST_INSERT = 'herc_quotes.herc_quotes.insert';

  /**
   * Name of the event fired after saving an existing jobquote.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEvent
   */
  const WISHLIST_UPDATE = 'herc_quotes.herc_quotes.update';

  /**
   * Name of the event fired before deleting a jobquote.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEvent
   */
  const WISHLIST_PREDELETE = 'herc_quotes.herc_quotes.predelete';

  /**
   * Name of the event fired after deleting a jobquote.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteEvent
   */
  const WISHLIST_DELETE = 'herc_quotes.herc_quotes.delete';

  /**
   * Name of the event fired after loading a jobquote item.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteItemEvent
   */
  const WISHLIST_ITEM_LOAD = 'herc_quotes.herc_quotes_item.load';

  /**
   * Name of the event fired after creating a jobquote item.
   *
   * Fired before the jobquote item is saved.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteItemEvent
   */
  const WISHLIST_ITEM_CREATE = 'herc_quotes.herc_quotes_item.create';

  /**
   * Name of the event fired before saving a jobquote item.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteItemEvent
   */
  const WISHLIST_ITEM_PRESAVE = 'herc_quotes.herc_quotes_item.presave';

  /**
   * Name of the event fired after saving a new jobquote item.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteItemEvent
   */
  const WISHLIST_ITEM_INSERT = 'herc_quotes.herc_quotes_item.insert';

  /**
   * Name of the event fired after saving an existing jobquote item.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteItemEvent
   */
  const WISHLIST_ITEM_UPDATE = 'herc_quotes.herc_quotes_item.update';

  /**
   * Name of the event fired before deleting a jobquote item.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteItemEvent
   */
  const WISHLIST_ITEM_PREDELETE = 'herc_quotes.herc_quotes_item.predelete';

  /**
   * Name of the event fired after deleting a jobquote item.
   *
   * @Event
   *
   * @see \Drupal\herc_quotes\Event\JobquoteItemEvent
   */
  const WISHLIST_ITEM_DELETE = 'herc_quotes.herc_quotes_item.delete';

}
