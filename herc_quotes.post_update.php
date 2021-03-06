<?php

/**
 * @file
 * Post update functions for Jobquote.
 */

/**
 * Revert the 'herc_quotes_item_table' view - fix broken handler.
 */
function herc_quotes_post_update_1() {
  /** @var \Drupal\commerce\Config\ConfigUpdaterInterface $config_updater */
  $config_updater = \Drupal::service('commerce.config_updater');
  $result = $config_updater->revert([
    'views.view.herc_quotes_item_table',
  ]);
  $message = implode('<br>', $result->getFailed());

  return $message;
}

/**
 * Revert the 'herc_quotes' view - remove the 'link_to_entity'.
 */
function herc_quotes_post_update_2() {
  /** @var \Drupal\commerce\Config\ConfigUpdaterInterface $config_updater */
  $config_updater = \Drupal::service('commerce.config_updater');
  $result = $config_updater->revert([
    'views.view.herc_quotes',
  ]);
  $message = implode('<br>', $result->getFailed());

  return $message;
}
