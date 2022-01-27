<?php

namespace Drupal\herc_quotes;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the interface for jobquote storage.
 */
interface JobquoteStorageInterface extends ContentEntityStorageInterface {

  /**
   * Loads the jobquote for the given code.
   *
   * @param string $code
   *   The code.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface|null
   *   The jobquote, or NULL if none found.
   */
  public function loadByCode($code);

  /**
   * Loads the default jobquote for the given user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user.
   * @param string $jobquote_type_id
   *   The jobquote type ID.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface|null
   *   The default jobquote for the given, if known.
   */
  public function loadDefaultByUser(AccountInterface $account, $jobquote_type_id);

  /**
   * Loads the given user's jobquotes.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user.
   * @param string $jobquote_type_id
   *   The jobquote type ID.
   *
   * @return \Drupal\profile\Entity\ProfileInterface[]
   *   The jobquotes, ordered by ID, descending.
   */
  public function loadMultipleByUser(AccountInterface $account, $jobquote_type_id);

}
