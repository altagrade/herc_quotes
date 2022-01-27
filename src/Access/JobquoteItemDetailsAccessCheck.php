<?php

namespace Drupal\herc_quotes\Access;

use Drupal\herc_quotes\JobquoteSessionInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access check for the jobquote item details_form route.
 */
class JobquoteItemDetailsAccessCheck implements AccessInterface {

  /**
   * The jobquote session.
   *
   * @var \Drupal\herc_quotes\JobquoteSessionInterface
   */
  protected $jobquoteSession;

  /**
   * Constructs a new JobquoteItemDetailsAccessCheck object.
   *
   * @param \Drupal\herc_quotes\JobquoteSessionInterface $jobquote_session
   *   The jobquote session.
   */
  public function __construct(JobquoteSessionInterface $jobquote_session) {
    $this->jobquoteSession = $jobquote_session;
  }

  /**
   * Checks access to the jobquote item details form.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The currently logged in account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(RouteMatchInterface $route_match, AccountInterface $account) {
    if ($account->hasPermission('administer herc_quotes')) {
      // Administrators can modify anyone's jobquote.
      $access = AccessResult::allowed()->cachePerPermissions();
    }
    else {
      // Users can modify their own jobquotes.
      /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
      $jobquote_item = $route_match->getParameter('herc_quotes_item');
      $user = $jobquote_item->getJobquote()->getOwner();

      if ($account->isAuthenticated()) {
        $access = AccessResult::allowedIf($user->id() === $account->id())
          ->addCacheableDependency($jobquote_item)
          ->cachePerUser();
      }
      else {
        $access = AccessResult::allowedIf($this->jobquoteSession->hasJobquoteId($jobquote_item->getJobquoteId()))
          ->addCacheableDependency($jobquote_item)
          ->addCacheContexts(['jobquote']);
      }
    }

    return $access;
  }

}
