<?php

namespace Drupal\herc_quotes\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'herc_quotes_item_table' formatter.
 *
 * @FieldFormatter(
 *   id = "herc_quotes_item_table",
 *   label = @Translation("jobquote item table"),
 *   field_types = {
 *     "entity_reference",
 *   },
 * )
 */
class JobquoteItemTable extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $jobquote = $items->getEntity();
    return [
      '#type' => 'view',
      // @todo Allow the view to be configurable.
      '#name' => 'herc_quotes_item_table',
      '#arguments' => [$jobquote->id()],
      '#embed' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $entity_type = $field_definition->getTargetEntityTypeId();
    $field_name = $field_definition->getName();
    return $entity_type == 'herc_quotes' && $field_name == 'jobquote_items';
  }

}
