<?php

namespace Drupal\moderation\Event;

use Drupal\Core\Entity\EntityInterface;
use Drupal\moderation\Entity\ModerationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when the moderation action is triggers.
 */
class ModerationEvent extends Event {

  const EVENT_NAME = 'modeartion_action';

  /**
   * The user account.
   *
   * @var \Drupal\user\UserInterface
   */
  public $entity;

  public $moderation;


  /**
   * ModerationEvent constructor.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param \Drupal\moderation\Entity\ModerationInterface $moderation
   */
  public function __construct(EntityInterface $entity, ModerationInterface $moderation) {
    $this->entity = $entity;
    $this->moderation = $moderation;
  }

  public function myEventDescription() {
    return "Foo";
  }

}
