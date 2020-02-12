<?php

namespace Drupal\moderation\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\moderation\Entity\ModerationTypeInterface;
use Drupal\moderation\Plugin\ModerationActionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EnrollmentController.
 */
class ModerationActionController extends ControllerBase {
  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccountProxy $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }


  public function action(ModerationTypeInterface $moderation_type, $entity_id, $action_name) {
    $action = $moderation_type->getAction($action_name);
    if ($action instanceof ModerationActionInterface) {
      $entity = $this->entityTypeManager()->getStorage($moderation_type->moderatedEntityType())->load($entity_id);
      $moderation = $moderation_type->getModerationEntity($entity);
      return $action->action($entity, $moderation);
    }
  }

}
