<?php

namespace Drupal\herc_quotes\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\profile\Entity\ProfileInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines the interface for jobquotes.
 */
interface JobquoteInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the jobquote code.
   *
   * @return string
   *   The jobquote code.
   */
  public function getCode();

  /**
   * Sets the jobquote code.
   *
   * @param string $code
   *   The jobquote code.
   *
   * @return $this
   */
  public function setCode($code);

  /**
   * Gets the jobquote name.
   *
   * @return string
   *   The jobquote name.
   */
  public function getName();

  /**
   * Sets the jobquote name.
   *
   * @param string $name
   *   The jobquote name.
   *
   * @return $this
   */
  public function setName($name);

  /**
   * Gets the shipping profile.
   *
   * @return \Drupal\profile\Entity\ProfileInterface|null
   *   The shipping profile, or null.
   */
  public function getShippingProfile();

  /**
   * Sets the shipping profile.
   *
   * @param \Drupal\profile\Entity\ProfileInterface $profile
   *   The shipping profile.
   *
   * @return $this
   */
  public function setShippingProfile(ProfileInterface $profile);

  /**
   * Gets the jobquote items.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteItemInterface[]
   *   The jobquote items.
   */
  public function getItems();

  /**
   * Sets the jobquote items.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface[] $jobquote_items
   *   The jobquote items.
   *
   * @return $this
   */
  public function setItems(array $jobquote_items);

  /**
   * Gets whether the jobquote has jobquote items.
   *
   * @return bool
   *   TRUE if the jobquote has jobquote items, FALSE otherwise.
   */
  public function hasItems();

  /**
   * Adds an jobquote item.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   *
   * @return $this
   */
  public function addItem(JobquoteItemInterface $jobquote_item);

  /**
   * Removes an jobquote item.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   *
   * @return $this
   */
  public function removeItem(JobquoteItemInterface $jobquote_item);

  /**
   * Checks whether the jobquote has a given jobquote item.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   *
   * @return bool
   *   TRUE if the jobquote item was found, FALSE otherwise.
   */
  public function hasItem(JobquoteItemInterface $jobquote_item);

  /**
   * Gets whether this is the user's default jobquote.
   *
   * @return bool
   *   TRUE if this is the user's default jobquote, FALSE otherwise.
   */
  public function isDefault();

  /**
   * Sets whether this is the user's default jobquote.
   *
   * @param bool $default
   *   Whether this is the user's default jobquote.
   *
   * @return $this
   */
  public function setDefault($default);

  /**
   * Gets whether the jobquote is public.
   *
   * @return bool
   *   TRUE if the jobquote is public, FALSE otherwise.
   */
  public function isPublic();

  /**
   * Sets whether the jobquote is public.
   *
   * @param bool $public
   *   Whether the jobquote is public.
   *
   * @return $this
   */
  public function setPublic($public);

  /**
   * Gets whether items should remain in the jobquote once purchased.
   *
   * @return bool
   *   TRUE if purchased items should remain in the jobquote, FALSE otherwise.
   */
  public function getKeepPurchasedItems();

  /**
   * Sets whether items should remain in the jobquote once purchased.
   *
   * @param bool $keep_purchased_items
   *   Whether items should remain in the jobquote once purchased.
   *
   * @return $this
   */
  public function setKeepPurchasedItems($keep_purchased_items);

  /**
   * Gets the jobquote creation timestamp.
   *
   * @return int
   *   Creation timestamp of the jobquote.
   */
  public function getCreatedTime();

  /**
   * Sets the jobquote creation timestamp.
   *
   * @param int $timestamp
   *   The jobquote creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

}
