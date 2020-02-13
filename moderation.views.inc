<?php

/**
 * @file
 * Providing extra functionality for the Moderation UI via views.
 */

use Drupal\Core\Entity\ContentEntityTypeInterface;

/**
 * Implementation of hook_views_data()
 */
function moderation_views_data_alter(&$data) {
  $data['moderation']['actions'] = array(
    'field' => array(
      'title' => t('Actions'),
      'help' => t('Provide the action Links of this moderation.'),
      'id' => 'moderation_handler_actions_field',
    ),
  );

  $entity_infos = \Drupal::entityTypeManager()->getDefinitions();
  foreach ($entity_infos as $machine_name => $entity_type) {
    if ($entity_type instanceof ContentEntityTypeInterface && $entity_type->getBundleEntityType()) {
      $base_table = $entity_type->getDataTable() ?: $entity_type->getBaseTable();
      $entity_keys = $entity_type->getKeys();

      if (!in_array($machine_name, ['moderation', 'moderation_type'])) {
        // Allow every entity exept moderation entities to join moderation.
        $data['moderation']['table']['join'][$base_table] = [
          'left_field' => $entity_keys['id'],
          'field' => 'entity_id',
          'extra' => [
            0 => [
              // Adds AND node_field_data.published = TRUE to the join.
              'field' => 'entity_type',
              'value' => $entity_type->id(),
            ],
          ],
        ];

      }
    }

  }
}
