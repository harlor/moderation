<?php


namespace Drupal\moderation\Plugin;


use Drupal\Core\Entity\EntityInterface;
use Drupal\moderation\Entity\ModerationInterface;
use Drupal\moderation\Entity\ModerationTypeInterface;

interface ModerationActionInterface {
  public function action(EntityInterface $entity, ModerationInterface $moderation);
  public function links(EntityInterface $entity, ModerationInterface $moderation);
}
