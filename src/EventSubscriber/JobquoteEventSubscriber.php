<?php

namespace Drupal\herc_quotes\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\herc_quotes\Event\JobquoteEntityAddEvent;
use Drupal\herc_quotes\Event\JobquoteEvents;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Defines the jobquote event subscriber.
 *
 * On adding an item to jobquote, the "added to job quote" message will be shown.
 */
class JobquoteEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new JobquoteEventSubscriber object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation.
   */
  public function __construct(MessengerInterface $messenger, TranslationInterface $string_translation) {
    $this->messenger = $messenger;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      JobquoteEvents::WISHLIST_ENTITY_ADD => 'displayAddToJobquoteMessage',
    ];
    return $events;
  }

  /**
   * Displays an add to jobquote message.
   *
   * @param \Drupal\herc_quotes\Event\JobquoteEntityAddEvent $event
   *   The add to jobquote event.
   */
  public function displayAddToJobquoteMessage(JobquoteEntityAddEvent $event) {
    $purchased_entity = $event->getEntity();
    $this->messenger->addStatus($this->t('@entity added to <a href=":url">your job quote</a>.', [
      '@entity' => $purchased_entity->label(),
      ':url' => Url::fromRoute('herc_quotes.page')->toString(),
    ]));
  }

}
