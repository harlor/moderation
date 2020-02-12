<?php

namespace Drupal\moderation\Plugin\Moderation;

use Drupal\Core\Entity\EntityInterface;
use Drupal\moderation\Entity\ModerationInterface;
use Drupal\moderation\Plugin\ModerationActionInterface;

/**
 * Plugin implementation of the 'text_plain' formatter.
 *
 * @ModerationAction(
 *   id = "event_dispatcher",
 *   label = @Translation("Event dispatcher"),
 * )
 */
class EventDispatcherModerationAction implements ModerationActionInterface {
  public function action() {

  }

  public function links(EntityInterface $entity, ModerationInterface $moderation) {
    return 'FOO';

  }

}
