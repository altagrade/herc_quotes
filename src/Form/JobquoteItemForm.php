<?php

namespace Drupal\herc_quotes\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\entity\Form\EntityDuplicateFormTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the jobquote item add/edit form.
 */
class JobquoteItemForm extends ContentEntityForm {

  use EntityDuplicateFormTrait;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs a new JobquoteItemForm object.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info, TimeInterface $time, DateFormatterInterface $date_formatter, EntityFieldManagerInterface $entity_field_manager) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);

    $this->dateFormatter = $date_formatter;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('date.formatter'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id) {
    if ($route_match->getRawParameter('herc_quotes_item') !== NULL) {
      $entity = $route_match->getParameter('herc_quotes_item');
    }
    else {
      /** @var \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote */
      $jobquote = $route_match->getParameter('herc_quotes');

      // Set parent jobquote id.
      $values = [
        'jobquote_id' => $jobquote->id(),
        'type' => 'commerce_product_variation',
      ];

      $entity = $this->entityTypeManager->getStorage('herc_quotes_item')->create($values);
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    $this->messenger()->addStatus($this->t('The item %label has been successfully saved.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.herc_quotes_item.collection', ['herc_quotes' => $this->entity->getJobquoteId()]);
  }

}
