<?php

namespace Drupal\herc_quotes\Form;

use Drupal\commerce_product\Entity\Product;
use Drupal\herc_quotes\Entity\JobquoteItem;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\herc_quotes\JobquoteProviderInterface;

/**
 * Defines the add to job quote form.
 */
class JobquoteAddProductForm extends FormBase {
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
     * The jobquote provider.
     *
     * @var \Drupal\herc_quotes\JobquoteProviderInterface
     */
  protected $jobquoteProvider;

  /**
     * Constructs a new JobquoteController object.
     *
     * @param \Drupal\Core\Session\AccountInterface $current_user
     *   The current user.
     * @param \Drupal\herc_quotes\JobquoteProviderInterface $jobquote_provider
     *   The jobquote provider.
     */
  public function __construct(AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager, JobquoteProviderInterface $jobquote_provider) {
        $this->currentUser = $current_user;
        $this->entityTypeManager = $entity_type_manager;
        $this->jobquoteProvider = $jobquote_provider;
      }

  /**
     * {@inheritdoc}
     */
  public static function create(ContainerInterface $container) {
        return new static(
            $container->get('current_user'),
            $container->get('entity_type.manager'),
            $container->get('herc_quotes.jobquote_provider')
          );
  }

  /**
     * Returns a unique string identifying the form.
     *
     * The returned ID should be a unique string that can be a valid PHP function
     * name, since it's used in hook implementation names such as
     * hook_form_FORM_ID_alter().
     *
     * @return string
     *   The unique string identifying the form.
     */
  public function getFormId() {
        return 'herc_quotes_add_product';
  }

  /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     * @param string
     *   The commerce product id which to add.
     *
     * @return array
     *   The form structure.
     */
  public function buildForm(array $form, FormStateInterface $form_state, $product_id = 0) {
        $options = [];
        foreach ($this->jobquoteProvider->getJobquotes() as $jobquote) {
            $options[$jobquote->id()] = $jobquote->getName();
          }

    $form_state->set('product_id', $product_id);

    $form['jobquotes'] = [
        '#type' => 'checkboxes',
        '#options' => $options,
        '#title' => $this->t('Job Quotes'),
        '#description' => $this->t('Add the product to one or multiple job quotes'),
        '#required' => true
        ];
    $form['quantity'] = [
        '#type' => 'number',
        '#title' => $this->t('Quantity'),
        '#default_value' => 1,
        '#required' => true
        ];
    $form['add'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add to job quote')
        ];

    return $form;
  }

  /**
     * {@inheritDoc}
     */
  public function validateForm(array &$form, FormStateInterface $form_state) {
        $product_id = $form_state->get('product_id');
        /** @var \Drupal\commerce_product\Entity\Product $product  */
        if (!$product = $this->entityTypeManager->getStorage('commerce_product')->load($product_id)) {
            $form_state->setErrorByName('product', $this->t('The product could not be found, please submit a valid product id'));
            return;
    }
    //@todo: this does not work with multiple variants.
    if (!$product_variation = $product->getDefaultVariation()) {
            $form_state->setErrorByName('product_variation', $this->t('No variation defined for product: @product_id', ['@product_id' => $product_id]));
            return;
    }

    $form_state->set('product_variation', $product_variation);
  }

  /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @throws \Drupal\Core\Entity\EntityStorageException
     */
  public function submitForm(array &$form, FormStateInterface $form_state) {
        /** @var \Drupal\commerce_product\Entity\ProductVariation $product_variation */
        $product_variation = $form_state->get('product_variation');
        $jobquote_ids = [];
        foreach ($form_state->getValue('jobquotes') as $jobquote_id) {
            if (!$jobquote_id) {
                continue;
      } else {
                $jobquote_ids[] = $jobquote_id;
              }
    }

    $jobquotes = $this->jobquoteProvider->getJobquotesById($jobquote_ids);
    $jobquote_names = [];
    foreach ($jobquotes as $id => $jobquote) {
            $item = JobquoteItem::create([
                'type' => 'commerce_product_variation',
                'purchasable_entity' => $product_variation,
                'quantity' => $form_state->getValue('quantity') ?? '1'
                ]);
            //@todo: combine the items when already in jobquote.
            $jobquote->addItem($item);
            $jobquote->save();

            $jobquote_names[] = $jobquote->getName();
          }

    $form_state->setRedirect('entity.commerce_product.canonical', ['commerce_product' => $form_state->get('product_id')]);
    $this->messenger()->addMessage($this->t('@product_title added to job quotes: @jobquote_name', [
        '@product_title' => $product_variation->getTitle(),
        '@jobquote_name' => implode(', ', $jobquote_names)]));
  }
}
