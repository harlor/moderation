<?php

namespace Drupal\moderation\Plugin\Moderation;

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

}
