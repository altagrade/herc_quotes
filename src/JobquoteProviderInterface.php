<?php

namespace Drupal\herc_quotes;

use Drupal\Core\Session\AccountInterface;

/**
 * Creates and loads jobquotes for anonymous and authenticated users.
 *
 * @see \Drupal\herc_quotes\JobquoteSessionInterface
 */
interface JobquoteProviderInterface {

  /**
   * Creates a jobquote entity for the given user.
   *
   * @param string $jobquote_type
   *   The jobquote type ID.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user. If empty, the current user is assumed.
   * @param string $name
   *   The jobquote name. Defaults to t('Jobquote').
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface
   *   The created jobquote entity.
   *
   * @throws \Drupal\herc_quotes\Exception\DuplicateJobquoteException
   *   When a jobquote with the given criteria already exists.
   */
  public function createJobquote($jobquote_type, AccountInterface $account = NULL, $name = NULL);

  /**
   * Gets the jobquote entity for the given user.
   *
   * @param string $jobquote_type
   *   The jobquote type ID.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user. If empty, the current user is assumed.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface|null
   *   The jobquote entity, or NULL if none found.
   */
  public function getJobquote($jobquote_type, AccountInterface $account = NULL);

  /**
   * Gets the jobquote entity ID for the given user.
   *
   * @param string $jobquote_type
   *   The jobquote type ID.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user. If empty, the current user is assumed.
   *
   * @return int|null
   *   The jobquote entity ID, or NULL if none found.
   */
  public function getJobquoteId($jobquote_type, AccountInterface $account = NULL);

  /**
   * Gets all jobquote entities for the given user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user. If empty, the current user is assumed.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface[]
   *   A list of jobquote entities.
   */
  public function getJobquotes(AccountInterface $account = NULL);

  /**
   * Gets all jobquote entity ids for the given user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user. If empty, the current user is assumed.
   *
   * @return int[]
   *   A list of jobquote entity ids.
   */
  public function getJobquoteIds(AccountInterface $account = NULL);

  /**
   * Load a jobquote by its id.
   *
   * @param int|array $id
   *   Single or multiple jobquote ids.
   *
   * @return \Drupal\herc_quotes\Entity\JobquoteInterface[]
   *   A list of jobquote entities.
   */
  public function getJobquotesById($id);

}
