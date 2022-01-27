<?php

namespace Drupal\herc_quotes;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Default implementation of the jobquote session.
 */
class JobquoteSession implements JobquoteSessionInterface {

  /**
   * The session.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * Constructs a new JobquoteSession object.
   *
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The session.
   */
  public function __construct(SessionInterface $session) {
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobquoteIds() {
    return $this->session->get('herc_quotes', []);
  }

  /**
   * {@inheritdoc}
   */
  public function addJobquoteId($jobquote_id) {
    $ids = $this->session->get('herc_quotes', []);
    $ids[] = $jobquote_id;
    $this->session->set('herc_quotes', array_unique($ids));
  }

  /**
   * {@inheritdoc}
   */
  public function hasJobquoteId($jobquote_id) {
    $ids = $this->session->get('herc_quotes', []);
    return in_array($jobquote_id, $ids);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteJobquoteId($jobquote) {
    $ids = $this->session->get('herc_quotes', []);
    $ids = array_diff($ids, [$jobquote]);
    if (!empty($ids)) {
      $this->session->set('herc_quotes', $ids);
    }
    else {
      // Remove the empty list to allow the system to clean up empty sessions.
      $this->session->remove('herc_quotes');
    }
  }

}
