<?php

namespace Drupal\herc_quotes\Plugin\Block;

use Drupal\herc_quotes\JobquoteProviderInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a jobquote block.
 *
 * @Block(
 *   id = "herc_quotes",
 *   admin_label = @Translation("Jobquote"),
 *   category = @Translation("Commerce")
 * )
 */
class JobquoteBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The jobquote provider.
   *
   * @var \Drupal\herc_quotes\JobquoteProviderInterface
   */
  protected $jobquoteProvider;

  /**
   * Constructs a new JobquoteBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\herc_quotes\JobquoteProviderInterface $jobquote_provider
   *   The jobquote provider.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, JobquoteProviderInterface $jobquote_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->jobquoteProvider = $jobquote_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('herc_quotes.jobquote_provider')
    );
  }

  /**
   * Builds the jobquote block.
   *
   * @return array
   *   A render array.
   */
  public function build() {
    $jobquotes = $this->jobquoteProvider->getJobquotes();

        $content = [];
        foreach ($jobquotes as $jobquote) {
            $count = $jobquotes ? count($jobquote->getItems()) : 0;

            $content[] = [
                'name' => $jobquote->getName(),
                'count' => $count,
                'count_text' => $this->formatPlural($count, '@count item', '@count items', [], ['context' => 'jobquote block']),
                'url' => $jobquote->toUrl()
                ];
          }

    return [
      '#theme' => 'herc_quotes_block',
            '#jobquote_entities' => $jobquotes,
            '#content' => $content,
            '#link' => Link::createFromRoute($this->t('Jobquotes'), 'herc_quotes.page')
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['jobquote']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $cache_tags = parent::getCacheTags();
    $jobquote_cache_tags = [];

    /** @var \Drupal\herc_quotes\Entity\JobquoteInterface[] $jobquotes */
    $jobquotes = $this->jobquoteProvider->getJobquotes();
    foreach ($jobquotes as $jobquote) {
      // Add tags for all jobquotes regardless items.
      $jobquote_cache_tags = Cache::mergeTags($jobquote_cache_tags, $jobquote->getCacheTags());
    }
    return Cache::mergeTags($cache_tags, $jobquote_cache_tags);
  }

}
