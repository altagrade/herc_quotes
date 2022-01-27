<?php

namespace Drupal\herc_quotes;

use Drupal\commerce\CommerceEntityViewsData;

/**
 * Provides views data for jobquote items.
 */
class JobquoteItemViewsData extends CommerceEntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['herc_quotes_item']['edit_quantity']['field'] = [
      'title' => t('Job quote quantity text field'),
      'help' => t('Adds a text field for editing the quantity.'),
      'id' => 'herc_quotes_item_edit_quantity',
    ];

    $data['herc_quotes_item']['remove_button']['field'] = [
      'title' => t('Remove button'),
      'help' => t('Adds a button for removing the job quote item.'),
      'id' => 'herc_quotes_item_remove_button',
    ];

    $data['herc_quotes_item']['move_to_cart']['field'] = [
      'title' => t('Move/copy to cart button'),
      'help' => t('Adds a button for moving or copying the job quote item to the shopping cart.'),
      'id' => 'herc_quotes_item_move_to_cart',
    ];

    return $data;
  }

}
