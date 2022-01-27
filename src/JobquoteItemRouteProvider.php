<?php

namespace Drupal\herc_quotes;

use Drupal\herc_quotes\Controller\JobquoteItemController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for the jobquote item entity.
 */
class JobquoteItemRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    if ($details_form_route = $this->getDetailsFormRoute($entity_type)) {
      $collection->add('entity.herc_quotes_item.details_form', $details_form_route);
    }

    return $collection;
  }

  /**
   * Gets the details-form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getDetailsFormRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('details-form'));
    $route
      ->addDefaults([
        '_controller' => JobquoteItemController::class . '::detailsForm',
        '_title' => 'Edit details',
      ])
      ->setRequirement('_jobquote_item_details_access_check', 'TRUE')
      ->setOption('parameters', [
        'herc_quotes_item' => ['type' => 'entity:herc_quotes_item'],
      ]);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getAddFormRoute($entity_type);
    $route->setOption('parameters', [
      'herc_quotes' => [
        'type' => 'entity:herc_quotes',
      ],
    ]);
    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getEditFormRoute($entity_type);
    $route->setOption('parameters', [
      'herc_quotes' => [
        'type' => 'entity:herc_quotes',
      ],
      'herc_quotes_item' => [
        'type' => 'entity:herc_quotes_item',
      ],
    ]);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDeleteFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getDeleteFormRoute($entity_type);
    $route->setOption('parameters', [
      'herc_quotes' => [
        'type' => 'entity:herc_quotes',
      ],
      'herc_quotes_item' => [
        'type' => 'entity:herc_quotes_item',
      ],
    ]);

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    $route = parent::getCollectionRoute($entity_type);
    $route
      ->setDefault('_title_callback', JobquoteItemController::class . '::collectionTitle')
      ->setOption('parameters', [
        'herc_quotes' => [
          'type' => 'entity:herc_quotes',
        ],
      ]);

    return $route;
  }

}
