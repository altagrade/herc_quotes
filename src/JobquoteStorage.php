<?php

namespace Drupal\herc_quotes;

use Drupal\commerce\CommerceContentEntityStorage;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the jobquote storage.
 */
class JobquoteStorage extends CommerceContentEntityStorage implements JobquoteStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadByCode($code) {
    $jobquotes = $this->loadByProperties(['code' => $code]);
    $jobquote = reset($jobquotes);

    return $jobquote ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function loadDefaultByUser(AccountInterface $account, $jobquote_type_id) {
    $query = $this->getQuery();
    $query
      ->condition('uid', $account->id())
      ->condition('is_default', TRUE)
      ->condition('type', $jobquote_type_id)
      ->sort('is_default', 'DESC')
      ->sort('jobquote_id', 'DESC')
      ->range(0, 1)
      ->accessCheck(FALSE);
    $result = $query->execute();

    return $result ? $this->load(reset($result)) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultipleByUser(AccountInterface $account, $jobquote_type_id) {
    $query = $this->getQuery();
    $query
      ->condition('uid', $account->id())
      ->condition('type', $jobquote_type_id)
      ->sort('is_default', 'DESC')
      ->sort('jobquote_id', 'DESC')
      ->accessCheck(FALSE);
    $result = $query->execute();

    return $result ? $this->loadMultiple($result) : [];
  }

}
