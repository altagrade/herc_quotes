<?php

namespace Drupal\herc_quotes\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\herc_quotes\JobquoteProviderInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides the jobquote pages.
 */
class JobquoteController implements ContainerInjectionInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

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
   * The jobquote provider.
   *
   * @var \Drupal\herc_quotes\JobquoteProviderInterface
   */
  protected $jobquoteProvider;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new JobquoteController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\herc_quotes\JobquoteProviderInterface $jobquote_provider
   *   The jobquote provider.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager, FormBuilderInterface $form_builder, RouteMatchInterface $route_match, JobquoteProviderInterface $jobquote_provider, LanguageManagerInterface $language_manager) {
    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->formBuilder = $form_builder;
    $this->routeMatch = $route_match;
    $this->jobquoteProvider = $jobquote_provider;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('form_builder'),
      $container->get('current_route_match'),
      $container->get('herc_quotes.jobquote_provider'),
      $container->get('language_manager')
    );
  }

  /**
   * Builds the jobquote page.
   *
   * If the customer doesn't have a jobquote, or the jobquote is empty,
   * the "empty page" will be shown. Otherwise, the customer will be redirected
   * to the default jobquote.
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array, or a redirect response.
   */
  public function jobquotePage() {
    $jobquote = $this->jobquoteProvider->getJobquote($this->getDefaultJobquoteType());
    if (!$jobquote || !$jobquote->hasItems()) {
      return [
        '#theme' => 'herc_quotes_empty_page',
        '#cache' => [
          'contexts' => ['user', 'session'],
        ],
      ];
    }
    // Authenticated users should always manage jobquotes via the user form.
    $rel = $this->currentUser->isAuthenticated() ? 'user-form' : 'canonical';
    $url = $jobquote->toUrl($rel, [
      'absolute' => TRUE,
      'language' => $this->languageManager->getCurrentLanguage(),
    ]);

    return new RedirectResponse($url->toString());
  }


  /**
   * Builds the user form.
   *
   * @return array
   *   The rendered form.
   */
  public function userForm() {
    $form_object = $this->getFormObject('user');
    $form_state = new FormState();

    return $this->formBuilder->buildForm($form_object, $form_state);
  }

  /**
   * Builds the share form.
   *
   * @return array
   *   The rendered form.
   */
  public function shareForm() {
    $form_object = $this->getFormObject('share');
    $form_state = new FormState();

    return $this->formBuilder->buildForm($form_object, $form_state);
  }

  /**
   * Gets the form object for the given operation.
   *
   * @param string $operation
   *   The operation.
   *
   * @return \Drupal\Core\Entity\EntityFormInterface
   *   The form object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown if no jobquote with the code specified in the URL could be found.
   */
  protected function getFormObject($operation) {
    $code = $this->routeMatch->getRawParameter('code');
    /** @var \Drupal\herc_quotes\JobquoteStorageInterface $jobquote_storage */
    $jobquote_storage = $this->entityTypeManager->getStorage('herc_quotes');
    $jobquote = $jobquote_storage->loadByCode($code);
    if (!$jobquote) {
      throw new NotFoundHttpException();
    }
    $form_object = $this->entityTypeManager->getFormObject('herc_quotes', $operation);
    $form_object->setEntity($jobquote);

    return $form_object;
  }

  /**
   * Gets the default jobquote type.
   */
  protected function getDefaultJobquoteType() {
    return $this->configFactory->get('herc_quotes.settings')->get('default_type');
  }

}
