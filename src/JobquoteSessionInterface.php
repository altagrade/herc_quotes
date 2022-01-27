<?php

namespace Drupal\herc_quotes;

/**
 * Stores jobquote ids in the anonymous user's session.
 *
 * Allows the system to keep track of which jobquote entities belong to the
 * anonymous user. The session is the only available storage in this case, since
 * all anonymous users share the same user id (0).
 *
 * @see \Drupal\herc_quotes\JobquoteProviderInterface
 */
interface JobquoteSessionInterface {

  /**
   * Gets all jobquote ids from the session.
   *
   * @return int[]
   *   A list of jobquote ids.
   */
  public function getJobquoteIds();

  /**
   * Adds the given jobquote ID to the session.
   *
   * @param int $jobquote_id
   *   The jobquote ID.
   */
  public function addJobquoteId($jobquote_id);

  /**
   * Checks whether the given jobquote ID exists in the session.
   *
   * @param int $jobquote_id
   *   The jobquote ID.
   *
   * @return bool
   *   TRUE if the given jobquote ID exists in the session, FALSE otherwise.
   */
  public function hasJobquoteId($jobquote_id);

  /**
   * Deletes the given jobquote ID from the session.
   *
   * @param int $jobquote_id
   *   The jobquote ID.
   */
  public function deleteJobquoteId($jobquote_id);

}
