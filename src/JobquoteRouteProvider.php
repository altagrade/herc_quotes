<?php

namespace Drupal\herc_quotes;

use Drupal\herc_quotes\Access\JobquoteUserAccessCheck;
use Drupal\herc_quotes\Controller\JobquoteController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for the jobquote entity.
 */
class JobquoteRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    if ($share_form_route = $this->getShareFormRoute($entity_type)) {
      $collection->add('entity.herc_quotes.share_form', $share_form_route);
    }
    if ($user_form_route = $this->getUserFormRoute($entity_type)) {
      $collection->add('entity.herc_quotes.user_form', $user_form_route);
    }

    return $collection;
  }

  /**
   * {@inheritdoc}
   */
  protected function getCanonicalRoute(EntityTypeInterface $entity_type) {
    $route = new Route('/jobquote/{code}');
    $route
      ->addDefaults([
        '_controller' => JobquoteController::class . '::userForm',
        '_title' => 'Job Quote',
      ])
      ->setRequirement('_access', 'TRUE');

    return $route;
  }

  /**
   * Gets the share-form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getShareFormRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('share-form'));
    $route
      ->addDefaults([
        '_controller' => JobquoteController::class . '::shareForm',
        '_title' => 'Job Quote',
      ])
      ->setRequirement('_custom_access', JobquoteUserAccessCheck::class . '::checkAccess')
      ->setOption('parameters', [
        'user' => ['type' => 'entity:user'],
      ]);

    return $route;
  }

  /**
   * Gets the user-form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getUserFormRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('user-form'));
    $route
      ->addDefaults([
        '_controller' => JobquoteController::class . '::userForm',
        '_title' => 'Job Quote',
      ])
      ->setRequirement('_custom_access', JobquoteUserAccessCheck::class . '::checkAccess')
      ->setOption('parameters', [
        'user' => ['type' => 'entity:user'],
      ]);

    return $route;
  }

}
