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

    $plugin_manager = \Drupal::service('plugin.manager.moderation_action');
    foreach (array_keys($this->actions) as $action) {
      /** @var \Drupal\moderation\Plugin\ModerationActionInterface $action_plugin */
      $action_plugin = $plugin_manager->createInstance($action);
      return $action_plugin->links($entity, $this->getModerationEntity($entity));
    }
  }

  public function getModerationEntity(EntityInterface $entity) {
    $moderations = $this->entityTypeManager()->getStorage('moderation')->loadByProperties(
      [
        'entity_type' => $entity->getEntityTypeId(),
        'entity_id' => $entity->bundle(),
        'type' => $this->id,
      ]
    );

    if (!empty($moderations)) {
      return $moderations[0];
    }

    return $this->entityTypeManager()->getStorage('moderation')->create([
      'entity_type' => $entity->getEntityTypeId(),
      'entity_id' => $entity->bundle(),
      'type' => $this->id,
    ]);
  }

  public function entityIsModerated(EntityInterface $entity) {
    return $this->entity_type == $entity->getEntityTypeId() && $this->bundle == $entity->bundle();
  }
}
