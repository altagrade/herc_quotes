<?php

namespace Drupal\herc_quotes\Form;

use Drupal\inline_entity_form\Form\EntityInlineForm;

/**
 * Defines the inline form for jobquote items.
 */
class JobquoteItemInlineForm extends EntityInlineForm {

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeLabels() {
    $labels = [
      'singular' => t('jobquote item'),
      'plural' => t('jobquote items'),
    ];
    return $labels;
  }

  /**
   * {@inheritdoc}
   */
  public function getTableFields($bundles) {
    $fields = parent::getTableFields($bundles);
    $fields['quantity'] = [
      'type' => 'field',
      'label' => t('Quantity'),
      'weight' => 2,
    ];

    return $fields;
  }

}
