<?php

namespace Drupal\moderation\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines the Moderation type entity.
 *
 * @ConfigEntityType(
 *   id = "moderation_type",
 *   label = @Translation("Moderation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\moderation\ModerationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\moderation\Form\ModerationTypeForm",
 *       "edit" = "Drupal\moderation\Form\ModerationTypeForm",
 *       "delete" = "Drupal\moderation\Form\ModerationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\moderation\ModerationTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "moderation_type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/moderation_type/{moderation_type}",
 *     "add-form" = "/admin/structure/moderation_type/add",
 *     "edit-form" = "/admin/structure/moderation_type/{moderation_type}/edit",
 *     "delete-form" = "/admin/structure/moderation_type/{moderation_type}/delete",
 *     "collection" = "/admin/structure/moderation_type"
 *   }
 * )
 */
class ModerationType extends ConfigEntityBase implements ModerationTypeInterface {

  /**
   * The Moderation type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Moderation type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Moderation entity type.
   *
   * @var string
   */
  protected $entity_type;

  /**
   * The Moderation entity bundle.
   *
   * @var string
   */
  protected $bundle;

  protected $actions;

  public function actionLinks(EntityInterface $entity) {
    $links = [];
    foreach ($this->getActions() as $action_plugin) {
      $links += $action_plugin->links($entity, $this->getModerationEntity($entity));
    }

    return $links;
  }

  public function getModerationEntity(EntityInterface $entity) {
    $moderations = $this->entityTypeManager()->getStorage('moderation')->loadByProperties(
      [
        'entity_type' => $entity->getEntityTypeId(),
        'entity_id' => $entity->id(),
        'type' => $this->id,
      ]
    );

    if (!empty($moderations)) {
      return current($moderations);
    }

    return $this->entityTypeManager()->getStorage('moderation')->create([
      'entity_type' => $entity->getEntityTypeId(),
      'entity_id' => $entity->id(),
      'type' => $this->id,
    ]);
  }

  public function entityIsModerated(EntityInterface $entity) {
    return $this->entity_type == $entity->getEntityTypeId() && $this->bundle == $entity->bundle();
  }

  public function getActions() {
    $plugin_manager = \Drupal::service('plugin.manager.moderation_action');
    $action_plugins = [];
    foreach($this->actions as $action_name => $enabled) {
      if ($enabled) {
        $action_plugins[] = $plugin_manager->createInstance($action_name);
      }
    }

    return $action_plugins;
  }

  public function getAction($action_name) {
    $plugin_manager = \Drupal::service('plugin.manager.moderation_action');
    if (isset($this->actions[$action_name]) && $this->actions[$action_name]) {
      return $plugin_manager->createInstance($action_name);
    }

    return FALSE;
  }

  public function moderatedEntityType() {
    return $this->entity_type;
  }
}
