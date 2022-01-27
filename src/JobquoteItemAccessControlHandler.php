<?php

namespace Drupal\herc_quotes;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides an access control handler for jobquote items.
 *
 * The "administer herc_quotes" permission is also respected.
 */
class JobquoteItemAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $entity */
    $jobquote = $entity->getJobquote();
    if (!$jobquote) {
      // The jobquote item is malformed.
      return AccessResult::forbidden()->addCacheableDependency($entity);
    }

    if ($operation == 'view') {
      $result = $jobquote->access('view', $account, TRUE);
    }
    else {
      $bundle = $entity->bundle();
      $result = AccessResult::allowedIfHasPermission($account, "manage $bundle herc_quotes_item");
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    // Create access depends on the "manage" permission because the full entity
    // is not passed, making it impossible to determine the parent jobquote.
    return AccessResult::allowedIfHasPermissions($account, [
      $this->entityType->getAdminPermission(),
      "manage $entity_bundle herc_quotes_item",
    ], 'OR');
  }

}
