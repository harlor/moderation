<?php

namespace Drupal\moderation\Entity;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Moderation entities.
 */
class ModerationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Moderation action links
    $data['moderation']['actions'] = array(
      'field' => array(
        'title' => t('Actions'),
        'help' => t('Provide the action Links of this moderation.'),
        'id' => 'moderation_handler_actions_field',
      ),
    );

    // Moderation status
    $data['moderation']['moderated'] = array(
      'title' => t('Moderated'),
      'help' => t('The moderation status'),
      'field' => array(
        'id' => 'boolean',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'filter' => array(
        'id' => 'boolean',
        // Override the generic field title, so that the filter uses a different
        // label in the UI.
        'label' => t('Moderated'),
        // Override the default BooleanOperator filter handler's 'type' setting,
        // to display this as a "Yes/No" filter instead of a "True/False" filter.
        'type' => 'yes-no',
        // Override the default Boolean filter handler's 'use_equal' setting, to
        // make the query use 'boolean_field = 1' instead of 'boolean_field <> 0'.
        'use_equal' => TRUE,
      ),
    );

    $entity_infos = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($entity_infos as $machine_name => $entity_type) {
      if ($entity_type instanceof ContentEntityTypeInterface && $entity_type->getBundleEntityType()) {
        $base_table = $entity_type->getDataTable() ?: $entity_type->getBaseTable();
        $entity_keys = $entity_type->getKeys();

        if (!in_array($machine_name, ['moderation', 'moderation_type']) && isset($entity_keys['id'])) {
          // Allow every entity exept moderation entities to join moderation.
          $data['moderation']['table']['join'][$base_table] = [
            'left_field' => $entity_keys['id'],
            'field' => 'entity_id',
            'extra' => [
              0 => [
                'field' => 'entity_type',
                'value' => $entity_type->id(),
              ],
            ],
          ];

        }
      }

    }

    return $data;
  }

}
