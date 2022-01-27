<?php

namespace Drupal\herc_quotes\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access check for jobquote user pages (user_form and share_form routes).
 */
class JobquoteUserAccessCheck {

  /**
   * Checks access to the given user's jobquote pages.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function checkAccess(RouteMatchInterface $route_match, AccountInterface $account) {
    if ($account->hasPermission('administer herc_quotes')) {
      // Administrators can modify anyone's jobquote.
      $access = AccessResult::allowed()->cachePerPermissions();
    }
    else {
      // Users can modify own jobquotes.
      $user = $route_match->getParameter('user');

      if ($account->isAuthenticated()) {
        $access = AccessResult::allowedIf($account->isAuthenticated())
          ->andIf(AccessResult::allowedIf($user->id() == $account->id()))
          ->cachePerUser();
      }
      else {
        $access = AccessResult::allowedIf($user->id() == '0')
          ->addCacheContexts(['jobquote']);
      }
    }

    return $access;
  }

}
