<?php

namespace Drupal\herc_quotes;

use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\herc_quotes\Entity\JobquoteItemInterface;

/**
 * Manages the jobquote and its jobquote items.
 */
interface JobquoteManagerInterface {

  /**
   * Empties the given jobquote entity.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote entity.
   * @param bool $save_jobquote
   *   Whether the jobquote should be saved after the operation.
   */
  public function emptyJobquote(JobquoteInterface $jobquote, $save_jobquote = TRUE);

  /**
   * Adds the given purchasable entity to the given jobquote entity.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote entity.
   * @param \Drupal\commerce\PurchasableEntityInterface $entity
   *   The purchasable entity.
   * @param int $quantity
   *   The quantity.
   * @param bool $combine
   *   Whether the jobquote item should be combined with an existing matching
   *   one.
   * @param bool $save_jobquote
   *   Whether the jobquote should be saved after the operation.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteItemInterface
   *   The saved jobquote item.
   */
  public function addEntity(JobquoteInterface $jobquote, PurchasableEntityInterface $entity, $quantity = 1, $combine = TRUE, $save_jobquote = TRUE);

  /**
   * Merges the source jobquote into the target jobquote.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $source
   *   The source jobquote to merge.
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $target
   *   The target jobquote.
   * @param bool $save
   *   Save jobquote.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface
   *   The saved or modified jobquote.
   */
  public function merge(JobquoteInterface $source, JobquoteInterface $target, $save = TRUE);

  /**
   * Removes the given jobquote item from the jobquote entity.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote entity.
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   * @param bool $save_jobquote
   *   Whether the jobquote should be saved after the operation.
   */
  public function removeJobquoteItem(JobquoteInterface $jobquote, JobquoteItemInterface $jobquote_item, $save_jobquote = TRUE);

}
