<?php

namespace Drupal\moderation;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Moderation entity.
 *
 * @see \Drupal\moderation\Entity\Moderation.
 */
class ModerationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\moderation\Entity\ModerationInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished moderation entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published moderation entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit moderation entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete moderation entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add moderation entities');
  }


}
