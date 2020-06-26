<?php

namespace Drupal\moderation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("moderation_handler_actions_field")
 */
class ModerationAction extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options
   *
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['moderation_type'] = ['default' => ''];

    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $options = [];

    /** @var \Drupal\moderation\Entity\ModerationType[] $moderation_types */
    $moderation_types = \Drupal::entityTypeManager()->getStorage('moderation_type')->loadMultiple();
    foreach ($moderation_types as $moderation_type) {
      $options[$moderation_type->id()] = $moderation_type->label();
    }

    $form['moderation_type'] = array(
      '#title' => $this->t('Which moderation type should be shown?'),
      '#type' => 'select',
      '#default_value' => $this->options['moderation_type'],
      '#options' => $options,
      '#multiple' => TRUE,
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $links = [];
    $entity = $values->_entity;
    /** @var \Drupal\moderation\Entity\ModerationType[] $moderation_types */
    $moderation_types = \Drupal::entityTypeManager()->getStorage('moderation_type')->loadMultiple();
    foreach ($moderation_types as $moderation_type) {
      if (in_array($moderation_type->id(), $this->options['moderation_type']) && $moderation_type->entityIsModerated($entity)) {
        $links += $moderation_type->actionLinks($entity);
      }
    }

    $content = [
      'container' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['container'],
        ],
      ],
    ];
    $content['container'] += $links;

    return $content;
  }
}
