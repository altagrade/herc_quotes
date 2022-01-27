<?php

namespace Drupal\herc_quotes\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the jobquote type entity class.
 *
 * @ConfigEntityType(
 *   id = "herc_quotes_type",
 *   label = @Translation("Jobquote type"),
 *   label_collection = @Translation("Jobquote types"),
 *   label_singular = @Translation("jobquote type"),
 *   label_plural = @Translation("jobquote types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count jobquote type",
 *     plural = "@count jobquote types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\herc_quotes\Form\JobquoteTypeForm",
 *       "edit" = "Drupal\herc_quotes\Form\JobquoteTypeForm",
 *       "delete" = "Drupal\commerce\Form\CommerceBundleEntityDeleteFormBase"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\herc_quotes\JobquoteTypeListBuilder",
 *   },
 *   admin_permission = "administer herc_quotes_type",
 *   config_prefix = "herc_quotes_type",
 *   bundle_of = "herc_quotes",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "allowAnonymous",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/jobquote-types/add",
 *     "edit-form" = "/admin/commerce/config/jobquote-types/{herc_quotes_type}/edit",
 *     "delete-form" = "/admin/commerce/config/jobquote-types/{herc_quotes_type}/delete",
 *     "collection" = "/admin/commerce/config/jobquote-types"
 *   }
 * )
 */
class JobquoteType extends ConfigEntityBundleBase implements JobquoteTypeInterface {

  /**
   * The jobquote type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The jobquote type label.
   *
   * @var string
   */
  protected $label;

  /**
   * Whether the jobquote item type allows anonymous jobquotes.
   *
   * @var bool
   */
  protected $allowAnonymous;

  /**
   * {@inheritdoc}
   */
  public function isAllowAnonymous() {
    return $this->allowAnonymous;
  }

  /**
   * {@inheritdoc}
   */
  public function setAllowAnonymous($allow_anonymous) {
    $this->allowAnonymous = $allow_anonymous;
    return $this;
  }

}
