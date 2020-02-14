<?php

namespace Drupal\moderation\Plugin\Moderation;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\custom_events\Event\ModerationEvent;
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
  use StringTranslationTrait;

  public function action(EntityInterface $entity, ModerationInterface $moderation) {
    // Instantiate our event.
    $event = new ModerationEvent($entity, $moderation);

    // Get the event_dispatcher service and dispatch the event.
    $event_dispatcher = \Drupal::service('event_dispatcher');
    $event_dispatcher->dispatch(ModerationEvent::EVENT_NAME, $event);

    $actions = [
      0 => 'moderate',
      1 => 'unmoderate',
    ];
    $action = $actions[intval($moderation->getDataValue('moderated'))];

    $moderation->set('data', ['moderated' => !$moderation->getDataValue('moderated')]);
    $moderation->save();

    $response = new AjaxResponse();
    $response->addCommand(new CssCommand('.' . $this->specificCssClass($entity, $moderation, $action), ['display' => 'none']));

    $action = $actions[intval($moderation->getDataValue('moderated'))];
    $response->addCommand(new CssCommand('.' . $this->specificCssClass($entity, $moderation, $action), ['display' => 'block']));

    return $response;
  }

  public function links(EntityInterface $entity, ModerationInterface $moderation) {
    $url = Url::fromRoute('moderation.action', [
      'moderation_type' => $moderation->getModerationType(),
      'entity_id' => $entity->id(),
      'action_name' => 'event_dispatcher',
    ]);

    $url2 = clone $url;

    $links = [
      [
        '#type' => 'link',
        '#title' => $this->t('unmoderate'),
        '#url' => $url,
        '#attributes' => [
          'class' => ['use-ajax', $this->specificCssClass($entity, $moderation, 'unmoderate')],
          'title' => $this->t('Trigger unpublish moderation action'),
        ],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('moderate'),
        '#url' => $url2,
        '#attributes' => [
          'class' => ['use-ajax', $this->specificCssClass($entity, $moderation, 'moderate')],
          'title' => $this->t('Trigger publish moderation action'),
        ],
      ],
    ];

    $links[intval($moderation->getDataValue('moderated'))]['#attributes']['class'][] = 'hidden';

    return $links;

  }


  protected function specificCssClass($entity, $moderation, $action) {
    return sprintf('%s-%s-%d', $action, $moderation->getModerationType(), $entity->id());
  }

}
