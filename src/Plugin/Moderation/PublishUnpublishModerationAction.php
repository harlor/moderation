<?php

namespace Drupal\moderation\Plugin\Moderation;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\moderation\Entity\ModerationInterface;
use Drupal\moderation\Plugin\ModerationActionInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'text_plain' formatter.
 *
 * @ModerationAction(
 *   id = "publish_unpublish",
 *   label = @Translation("Publish unpublish"),
 * )
 */
class PublishUnpublishModerationAction implements ModerationActionInterface {
  use StringTranslationTrait;

  public function action(EntityInterface $entity, ModerationInterface $moderation) {
    $actions = [
      0 => 'publish',
      1 => 'unpublish',
    ];
    $action = $actions[intval($entity->isPublished())];

    $entity->setPublished(!$entity->isPublished());
    $entity->save();

    $response = new AjaxResponse();
    $response->addCommand(new CssCommand('.' . $this->specificCssClass($entity, $moderation, $action), ['display' => 'none']));

    $action = $actions[intval($entity->isPublished())];
    $response->addCommand(new CssCommand('.' . $this->specificCssClass($entity, $moderation, $action), ['display' => 'block']));

    return $response;
  }

  public function links(EntityInterface $entity, ModerationInterface $moderation) {
    $url = Url::fromRoute('moderation.action', [
      'moderation_type' => $moderation->getModerationType(),
      'entity_id' => $entity->id(),
      'action_name' => 'publish_unpublish',
    ]);

    $url2 = clone $url;

    $links = [
      [
        '#type' => 'link',
        '#title' => $this->t('unpublish'),
        '#url' => $url,
        '#attributes' => [
          'class' => ['use-ajax', $this->specificCssClass($entity, $moderation, 'unpublish')],
          'title' => $this->t('Trigger unpublish moderation action'),
        ],
      ],
      [
        '#type' => 'link',
        '#title' => $this->t('publish'),
        '#url' => $url2,
        '#attributes' => [
          'class' => ['use-ajax', $this->specificCssClass($entity, $moderation, 'publish')],
          'title' => $this->t('Trigger publish moderation action'),
        ],
      ],
    ];

    $links[intval($entity->isPublished())]['#attributes']['class'][] = 'hidden';

    return $links;

  }

  protected function specificCssClass($entity, $moderation, $action) {
    return sprintf('%s-%s-%d', $action, $moderation->getModerationType(), $entity->id());
  }

}
