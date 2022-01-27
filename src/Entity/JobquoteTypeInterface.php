<?php

namespace Drupal\herc_quotes\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines the interface for jobquote types.
 */
interface JobquoteTypeInterface extends ConfigEntityInterface {

  /**
   * Gets whether the jobquote item type allows anonymous jobquotes.
   *
   * @return bool
   *   TRUE if anonymous jobquotes are allowed, FALSE otherwise.
   */
  public function isAllowAnonymous();

  /**
   * Sets whether the jobquote item type allows anonymous jobquotes.
   *
   * @param bool $allow_anonymous
   *   Whether the jobquote item type allows anonymous jobquotes.
   *
   * @return $this
   */
  public function setAllowAnonymous($allow_anonymous);

}
