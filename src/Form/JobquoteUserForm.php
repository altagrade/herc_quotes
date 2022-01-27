<?php

namespace Drupal\herc_quotes\Form;

use Drupal\commerce\AjaxFormTrait;
use Drupal\commerce\Context;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_order\Resolver\OrderTypeResolverInterface;
use Drupal\commerce_price\Resolver\ChainPriceResolverInterface;
use Drupal\commerce_store\CurrentStoreInterface;
use Drupal\herc_quotes\Entity\JobquoteInterface;
use Drupal\herc_quotes\Entity\JobquoteItemInterface;
use Drupal\herc_quotes\JobquoteManagerInterface;
use Drupal\herc_quotes\JobquoteSessionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the jobquote user form.
 *
 * Used for both the canonical ("/quotes/{code}") and user-form
 * ("/user/{user}/quotes/{herc_quotes}") pages.
 */
class JobquoteUserForm extends EntityForm {

  use AjaxFormTrait;

  /**
   * The cart manager.
   *
   * @var \Drupal\commerce_cart\CartManagerInterface
   */
  protected $cartManager;

  /**
   * The cart provider.
   *
   * @var \Drupal\commerce_cart\CartProviderInterface
   */
  protected $cartProvider;

  /**
   * The current store.
   *
   * @var \Drupal\commerce_store\CurrentStoreInterface
   */
  protected $currentStore;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The order type resolver.
   *
   * @var \Drupal\commerce_order\Resolver\OrderTypeResolverInterface
   */
  protected $orderTypeResolver;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The jobquote settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $settings;

  /**
   * The chain base price resolver.
   *
   * @var \Drupal\commerce_price\Resolver\ChainPriceResolverInterface
   */
  protected $chainPriceResolver;

  /**
   * The jobquote manager.
   *
   * @var \Drupal\herc_quotes\JobquoteManagerInterface
   */
  protected $jobquoteManager;

  /**
   * The jobquote session.
   *
   * @var \Drupal\herc_quotes\JobquoteSessionInterface
   */
  protected $jobquoteSession;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new JobquoteUserForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\commerce_cart\CartManagerInterface $cart_manager
   *   The cart manager.
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider.
   * @param \Drupal\commerce_store\CurrentStoreInterface $current_store
   *   The current store.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\commerce_order\Resolver\OrderTypeResolverInterface $order_type_resolver
   *   The order type resolver.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\commerce_price\Resolver\ChainPriceResolverInterface $chain_price_resolver
   *   The price resolver.
   * @param \Drupal\herc_quotes\JobquoteManagerInterface $jobquote_manager
   *   The jobquote manager.
   * @param \Drupal\herc_quotes\JobquoteSessionInterface $jobquote_session
   *   The jobquote session.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, CartManagerInterface $cart_manager, CartProviderInterface $cart_provider, CurrentStoreInterface $current_store, AccountInterface $current_user, OrderTypeResolverInterface $order_type_resolver, RouteMatchInterface $route_match, ChainPriceResolverInterface $chain_price_resolver, JobquoteManagerInterface $jobquote_manager, JobquoteSessionInterface $jobquote_session, LanguageManagerInterface $language_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cartManager = $cart_manager;
    $this->cartProvider = $cart_provider;
    $this->currentStore = $current_store;
    $this->currentUser = $current_user;
    $this->orderTypeResolver = $order_type_resolver;
    $this->routeMatch = $route_match;
    $this->settings = $config_factory->get('herc_quotes.settings');
    $this->chainPriceResolver = $chain_price_resolver;
    $this->jobquoteManager = $jobquote_manager;
    $this->jobquoteSession = $jobquote_session;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
      $container->get('commerce_cart.cart_manager'),
      $container->get('commerce_cart.cart_provider'),
      $container->get('commerce_store.current_store'),
      $container->get('current_user'),
      $container->get('commerce_order.chain_order_type_resolver'),
      $container->get('current_route_match'),
      $container->get('commerce_price.chain_price_resolver'),
      $container->get('herc_quotes.jobquote_manager'),
      $container->get('herc_quotes.jobquote_session'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote */
    $jobquote = $this->entity;
    $owner_access = $this->ownerAccess($jobquote);
    $jobquote_has_items = $jobquote->hasItems();

    $form['#tree'] = TRUE;
    $form['#process'][] = '::processForm';
    $form['#theme'] = 'herc_quotes_user_form';
    $form['#attached']['library'][] = 'herc_quotes/user';
    // Workaround for core bug #2897377.
    $form['#id'] = Html::getId($form_state->getBuildInfo()['form_id']);

    $form['header'] = [
      '#type' => 'container',
    ];
    $form['header']['empty_text'] = [
      '#markup' => $this->t('Your job quote is empty.'),
      '#access' => !$jobquote_has_items,
    ];
    $form['header']['add_all_to_cart'] = [
      '#type' => 'submit',
      '#value' => t('Order this quote'),
      '#ajax' => [
        'callback' => [get_called_class(), 'ajaxRefreshForm'],
      ],
      '#access' => $jobquote_has_items,
    ];
    /*
     * Alex: let's hide email feature for now as it's not properly working.
    $form['header']['share'] = [
      '#type' => 'link',
      '#title' => $this->t('Share the list by email'),
      '#url' => $jobquote->toUrl('share-form', [
        'language' => $this->languageManager->getCurrentLanguage(),
      ]),
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
          'btn',
          'btn-default',
          'jobquote-button',
        ],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'width' => 700,
          'title' => $this->t('Share the list by email'),
        ]),
        'role' => 'button',
      ],
      '#access' => $owner_access && $jobquote_has_items,
    ];
    */

    $form['items'] = [];
    foreach ($jobquote->getItems() as $item) {
      $purchasable_entity = $item->getPurchasableEntity();
      if (!$purchasable_entity) {
        continue;
      }
      $item_form = &$form['items'][$item->id()];

      $item_form = [
        '#type' => 'container',
      ];
      $item_form['entity'] = $this->renderPurchasableEntity($purchasable_entity);
      $item_form['details'] = [
        '#theme' => 'herc_quotes_item_details',
        '#jobquote_item_entity' => $item,
      ];
      $item_form['details_edit'] = [
        '#type' => 'link',
        '#title' => $this->t('edit'),
        '#url' => $item->toUrl('details-form'),
        '#attributes' => [
          'class' => [
            'use-ajax',
            'jobquote-item__details-edit-link',
          ],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 700,
            'title' => $this->t('Edit'),
          ]),
        ],
        '#access' => $owner_access,
      ];
      $item_form['actions'] = [
        '#type' => 'container',
      ];
      $item_form['actions']['add_to_cart'] = [
        '#type' => 'submit',
        '#value' => t('Add to cart'),
        '#ajax' => [
          'callback' => [get_called_class(), 'ajaxRefreshForm'],
        ],
        '#submit' => [
          '::addToCartSubmit',
        ],
        '#name' => 'add-to-cart-' . $item->id(),
        '#item_id' => $item->id(),
      ];
      $item_form['actions']['remove'] = [
        '#type' => 'submit',
        '#value' => t('Remove'),
        '#ajax' => [
          'callback' => [get_called_class(), 'ajaxRefreshForm'],
        ],
        '#submit' => [
          '::removeItem',
        ],
        '#name' => 'remove-' . $item->id(),
        '#access' => $owner_access,
        '#item_id' => $item->id(),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actionsElement(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * Submit callback for the "Add to cart" button.
   */
  public function addToCartSubmit(array $form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $jobquote_item_storage = $this->entityTypeManager->getStorage('herc_quotes_item');
    /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
    $jobquote_item = $jobquote_item_storage->load($triggering_element['#item_id']);
    $this->addItemToCart($jobquote_item);
  }

  /**
   * Submit callback for the "Remove" button.
   */
  public function removeItem(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote */
    $jobquote = $this->entity;
    $triggering_element = $form_state->getTriggeringElement();
    $jobquote_item_storage = $this->entityTypeManager->getStorage('herc_quotes_item');
    /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
    $jobquote_item = $jobquote_item_storage->load($triggering_element['#item_id']);
    $this->jobquoteManager->removeJobquoteItem($jobquote, $jobquote_item);

    $this->messenger()->addStatus($this->t('@entity has been removed from your job quote.', [
      '@entity' => $jobquote_item->label(),
    ]));
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote */
    $jobquote = $this->entity;
    foreach ($jobquote->getItems() as $jobquote_item) {
      $this->addItemToCart($jobquote_item);
    }
  }

  /**
   * Renders the given purchasable entity.
   *
   * @param \Drupal\commerce\PurchasableEntityInterface $purchasable_entity
   *   The purchasable entity.
   *
   * @return array
   *   The render array.
   */
  protected function renderPurchasableEntity(PurchasableEntityInterface $purchasable_entity) {
    $entity_type_id = $purchasable_entity->getEntityTypeId();
    $view_builder = $this->entityTypeManager->getViewBuilder($entity_type_id);
    $view_mode = $this->settings->get('view_modes.' . $entity_type_id);
    $view_mode = $view_mode ?: 'jobquote';
    $build = $view_builder->view($purchasable_entity, $view_mode);
    return $build;
  }

  /**
   * Checks whether the current user owns the given job quote.
   *
   * Used to determine whether the user is allowed to modify and share
   * the jobquote.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteInterface $jobquote
   *   The jobquote.
   *
   * @return bool
   *   TRUE if the current user owns the given jobquote, FALSE otherwise.
   */
  protected function ownerAccess(JobquoteInterface $jobquote) {
    if ($this->currentUser->isAnonymous()) {
      // Anonymous job quotes aren't fully implemented yet.
      return $this->jobquoteSession->hasJobquoteId($jobquote->id());
    }
    if ($jobquote->getOwnerId() != $this->currentUser->id()) {
      return FALSE;
    }
    if ($this->routeMatch->getRouteName() != 'entity.herc_quotes.user_form') {
      // Users should only modify their job quotes via the user form.
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Adds a job quote item to the cart.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item to move to the cart.
   */
  protected function addItemToCart(JobquoteItemInterface $jobquote_item) {
    $purchasable_entity = $jobquote_item->getPurchasableEntity();
    /** @var \Drupal\commerce_order\OrderItemStorageInterface $order_item_storage */
    $order_item_storage = $this->entityTypeManager->getStorage('commerce_order_item');
    $values = [
      'quantity' => $jobquote_item->getQuantity(),
    ];
    $order_item = $order_item_storage->createFromPurchasableEntity($purchasable_entity, $values);
    $order_type_id = $this->orderTypeResolver->resolve($order_item);
    $store = $this->selectStore($purchasable_entity);
    $cart = $this->cartProvider->getCart($order_type_id, $store);
    if (!$order_item->isUnitPriceOverridden()) {
      $context = new Context($this->currentUser, $store);
      $resolved_price = $this->chainPriceResolver->resolve($purchasable_entity, $order_item->getQuantity(), $context);
      $order_item->setUnitPrice($resolved_price);
    }
    if (!$cart) {
      $cart = $this->cartProvider->createCart($order_type_id, $store);
    }
    $this->cartManager->addOrderItem($cart, $order_item, TRUE);
  }

  /**
   * Selects the store for the given purchasable entity.
   *
   * Copied over from AddToCartForm.
   *
   * If the entity is sold from one store, then that store is selected.
   * If the entity is sold from multiple stores, and the current store is
   * one of them, then that store is selected.
   *
   * @param \Drupal\commerce\PurchasableEntityInterface $entity
   *   The entity being added to cart.
   *
   * @throws \Exception
   *   When the entity can't be purchased from the current store.
   *
   * @return \Drupal\commerce_store\Entity\StoreInterface
   *   The selected store.
   */
  protected function selectStore(PurchasableEntityInterface $entity) {
    $stores = $entity->getStores();
    if (count($stores) === 1) {
      $store = reset($stores);
    }
    elseif (count($stores) === 0) {
      // Malformed entity.
      throw new \Exception('The given entity is not assigned to any store.');
    }
    else {
      $store = $this->currentStore->getStore();
      if (!in_array($store, $stores)) {
        // Indicates that the site listings are not filtered properly.
        throw new \Exception("The given entity can't be purchased from the current store.");
      }
    }

    return $store;
  }

}
