<?php


namespace Drupal\moderation\Plugin;


use Drupal\Core\Entity\EntityInterface;
use Drupal\moderation\Entity\ModerationInterface;

interface ModerationActionInterface {
  public function action();
  public function links(EntityInterface $entity, ModerationInterface $moderation);
}
