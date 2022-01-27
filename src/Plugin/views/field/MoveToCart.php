<?php

namespace Drupal\herc_quotes\Plugin\views\field;

use Drupal\commerce_cart\CartManagerInterface;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_order\Resolver\OrderTypeResolverInterface;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\commerce_store\CurrentStoreInterface;
use Drupal\commerce_store\SelectStoreTrait;
use Drupal\herc_quotes\Entity\JobquoteItemInterface;
use Drupal\herc_quotes\JobquoteManagerInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\Plugin\views\field\UncacheableFieldHandlerTrait;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form element for moving or copying the jobquote item to the cart.
 *
 * @ViewsField("herc_quotes_item_move_to_cart")
 */
class MoveToCart extends FieldPluginBase {

  use SelectStoreTrait;

  use UncacheableFieldHandlerTrait;

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
   * The order type resolver.
   *
   * @var \Drupal\commerce_order\Resolver\OrderTypeResolverInterface
   */
  protected $orderTypeResolver;

  /**
   * The current store.
   *
   * @var \Drupal\commerce_store\CurrentStoreInterface
   */
  protected $currentStore;

  /**
   * The jobquote manager.
   *
   * @var \Drupal\herc_quotes\JobquoteManagerInterface
   */
  protected $jobquoteManager;

  /**
   * Constructs a new MoveToCart object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\herc_quotes\JobquoteManagerInterface $jobquote_manager
   *   The jobquote manager.
   * @param \Drupal\commerce_cart\CartManagerInterface $cart_manager
   *   The cart manager.
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider.
   * @param \Drupal\commerce_order\Resolver\OrderTypeResolverInterface $order_type_resolver
   *   The order type resolver.
   * @param \Drupal\commerce_store\CurrentStoreInterface $current_store
   *   The current store.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, JobquoteManagerInterface $jobquote_manager, CartManagerInterface $cart_manager, CartProviderInterface $cart_provider, OrderTypeResolverInterface $order_type_resolver, CurrentStoreInterface $current_store) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->jobquoteManager = $jobquote_manager;
    $this->cartManager = $cart_manager;
    $this->cartProvider = $cart_provider;
    $this->orderTypeResolver = $order_type_resolver;
    $this->currentStore = $current_store;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('herc_quotes.jobquote_manager'),
      $container->get('commerce_cart.cart_manager'),
      $container->get('commerce_cart.cart_provider'),
      $container->get('commerce_order.chain_order_type_resolver'),
      $container->get('commerce_store.current_store')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function clickSortable() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $row, $field = NULL) {
    return '<!--form-item-' . $this->options['id'] . '--' . $row->index . '-->';
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['keep_item'] = ['default' => FALSE];
    $options['combine'] = ['default' => TRUE];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['keep_item'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Keep item'),
      '#description' => $this->t('Enable in order to keep the item in the jobquote (copy instead of move).'),
      '#default_value' => $this->options['keep_item'],
    ];

    $form['combine'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Combine'),
      '#description' => $this->t('Combine order items containing the same product variation.'),
      '#default_value' => $this->options['combine'],
    ];
  }

  /**
   * Form constructor for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function viewsForm(array &$form, FormStateInterface $form_state) {
    // Make sure we do not accidentally cache this form.
    $form['#cache']['max-age'] = 0;
    // The view is empty, abort.
    if (empty($this->view->result)) {
      unset($form['actions']);
      return;
    }

    $form[$this->options['id']]['#tree'] = TRUE;
    foreach ($this->view->result as $row_index => $row) {
      /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
      $jobquote_item = $this->getEntity($this->view->result[$row_index]);

      if ($this->isValid($jobquote_item)) {
        $form[$this->options['id']][$row_index] = [
          '#type' => 'submit',
          '#value' => $this->options['keep_item'] ? $this->t('Copy to cart') : $this->t('Move to cart'),
          '#name' => 'move-jobquote-item-' . $row_index,
          '#move_jobquote_item' => TRUE,
          '#row_index' => $row_index,
          '#attributes' => ['class' => ['move-jobquote-item']],
        ];
      }
      else {
        $form[$this->options['id']][$row_index] = [];
      }
    }
  }

  /**
   * Submit handler for the views form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @throws \Exception
   *   When the call to self::selectStore() throws an exception because the
   *   entity can't be purchased from the current store.
   */
  public function viewsFormSubmit(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    if (!empty($triggering_element['#move_jobquote_item'])) {
      $row_index = $triggering_element['#row_index'];
      /** @var \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item */
      $jobquote_item = $this->getEntity($this->view->result[$row_index]);

      $purchased_entity = $jobquote_item->getPurchasableEntity();
      $order_item = $this->cartManager->createOrderItem($purchased_entity, $jobquote_item->getQuantity());
      $order_type = $this->orderTypeResolver->resolve($order_item);

      $store = $this->selectStore($purchased_entity);
      $cart = $this->cartProvider->getCart($order_type, $store);
      if (!$cart) {
        $cart = $this->cartProvider->createCart($order_type, $store);
      }
      $this->cartManager->addOrderItem($cart, $order_item, $this->options['combine']);
      if (!$this->options['keep_item']) {
        $jobquote = $jobquote_item->getJobquote();
        $jobquote->removeItem($jobquote_item);
        $jobquote->save();
        $jobquote_item->delete();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing.
  }

  /**
   * Checks, if the given jobquote item is still valid.
   *
   * The underlying purchasable entity could have been deleted or disabled in
   * the meantime. In this case, we won't show the move/copy to cart action at
   * all.
   *
   * @param \Drupal\herc_quotes\Entity\JobquoteItemInterface $jobquote_item
   *   The jobquote item.
   *
   * @return bool
   *   TRUE, if the given jobquote item is valid, FALSE otherwise.
   */
  protected function isValid(JobquoteItemInterface $jobquote_item) {
    $purchasable_entity = $jobquote_item->getPurchasableEntity();
    $valid = FALSE;
    // Check the deprecated ::isActive method.
    if ($purchasable_entity instanceof ProductVariationInterface) {
      $valid = $purchasable_entity->isActive();
    }
    elseif ($purchasable_entity instanceof EntityPublishedInterface) {
      $valid = $purchasable_entity->isPublished();
    }
    if ($valid) {
      try {
        $this->selectStore($jobquote_item->getPurchasableEntity());
      }
      catch (\Exception $e) {
        $valid = FALSE;
      }
    }
    return $valid;
  }

}
