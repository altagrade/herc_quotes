<?php

namespace Drupal\herc_quotes\Controller;

use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the jobquote item pages.
 */
class JobquoteItemController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new JobquoteController object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation.
   */
  public function __construct(AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager, FormBuilderInterface $form_builder, RouteMatchInterface $route_match, TranslationInterface $string_translation) {
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->formBuilder = $form_builder;
    $this->routeMatch = $route_match;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('form_builder'),
      $container->get('current_route_match'),
      $container->get('string_translation')
    );
  }

  /**
   * Builds the item details form.
   *
   * @return array
   *   The rendered form.
   */
  public function detailsForm() {
    $jobquote_item = $this->routeMatch->getParameter('herc_quotes_item');
    $form_object = $this->entityTypeManager->getFormObject('herc_quotes_item', 'details');
    $form_object->setEntity($jobquote_item);
    $form_state = new FormState();

    return $this->formBuilder->buildForm($form_object, $form_state);
  }

  /**
   * Provides the title callback for the jobquote items collection route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   *
   * @return string
   *   The title.
   */
  public function collectionTitle(RouteMatchInterface $route_match) {
    $jobquote = $route_match->getParameter('herc_quotes');
    assert($jobquote instanceof JobquoteInterface);
    return $this->t('%label items', ['%label' => $jobquote->label()]);
  }

}
