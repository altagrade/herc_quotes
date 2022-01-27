<?php

namespace Drupal\herc_quotes\Cache\Context;

use Drupal\herc_quotes\JobquoteProviderInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the JobquoteCacheContext service, for "per jobquote" caching.
 *
 * Cache context ID: 'jobquote'.
 */
class JobquoteCacheContext implements CacheContextInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The jobquote provider service.
   *
   * @var \Drupal\herc_quotes\JobquoteProviderInterface
   */
  protected $jobquoteProvider;

  /**
   * Constructs a new JobquoteCacheContext object.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param \Drupal\herc_quotes\JobquoteProviderInterface $jobquote_provider
   *   The jobquote provider service.
   */
  public function __construct(AccountInterface $account, JobquoteProviderInterface $jobquote_provider) {
    $this->account = $account;
    $this->jobquoteProvider = $jobquote_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return t('Current jobquote IDs');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext() {
    return implode(':', $this->jobquoteProvider->getJobquoteIds($this->account));
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    $metadata = new CacheableMetadata();
    foreach ($this->jobquoteProvider->getJobquotes($this->account) as $jobquote) {
      $metadata->addCacheableDependency($jobquote);
    }
    return $metadata;
  }

}
