<?php

namespace Drupal\moderation\Form;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ModerationTypeForm.
 */
class ModerationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $moderation_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $moderation_type->label(),
      '#description' => $this->t("Label for the Moderation type."),
      '#required' => TRUE,
      '#weight' => 0,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $moderation_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\moderation\Entity\ModerationType::load',
      ],
      '#disabled' => !$moderation_type->isNew(),
      '#weight' => 1,
    ];

    $entity_type_options = [];
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type) {
      if ($entity_type instanceof ContentEntityTypeInterface && $entity_type->getBundleEntityType()) {
        $bundles = $this->entityTypeManager->getStorage($entity_type->getBundleEntityType())->loadMultiple();
        $entity_type_options[$entity_type->id()] = $entity_type->get('label');
        $bundle_options = array_map(function ($bundle) {return !empty($bundle->get('label')) ? $bundle->get('label') : $bundle->get('name');}, $bundles);
        $entity_type_bundle_options[$entity_type->id()] = $bundle_options;

        $form[$entity_type->id() . '_bundle'] = [
          '#type' => 'select',
          '#title' => $this->t('Bundle'),
          '#description' => $this->t('The bundle type to be moderated.'),
          '#options' => $bundle_options,
          '#states' => [
            'visible' => [
              ':input[name="entity_type"]' => ['value' => $entity_type->id()],
            ]
          ],
          '#weight' => 3,
        ];
      }

    }

    $form[$moderation_type->get('entity_type') . '_bundle']['#default_value'] = $moderation_type->get('bundle');

    $form['entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Entity Type'),
      '#description' => $this->t('The entity type to be moderated.'),
      '#options' => $entity_type_options,
      '#default_value' => $moderation_type->get('entity_type'),
      '#weight' => 2,
    ];

    $form['moderation_action'] = [
      '#weight' => 4,
      '#description' => $this->t('Actions to perform when triggered.'),
    ];

    $action_plugin_manager = \Drupal::service('plugin.manager.moderation_action');
    $actions = $moderation_type->get('actions');

    foreach ($action_plugin_manager->getDefinitions() as $definition) {
      $key = 'moderation_action_' . $definition['id'];
      $form['moderation_action'][$key] = [
        '#title' => $definition['label'],
        '#type' => 'checkbox',
        '#default_value' => $actions[$key],
        '#plugin' => $definition['id'],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $moderation_type = $this->entity;
    $moderation_type->set('entity_type', $form_state->getValue('entity_type'));
    $moderation_type->set('bundle', $form_state->getValue($form_state->getValue('entity_type') . '_bundle'));
    $actions = [];
    foreach ($form['moderation_action'] as $key => $item) {
      if (substr($key, 0, 1) != '#') {
        $actions[$item['#plugin']] = $form_state->getValue($key);
      }
    }
    $moderation_type->set('actions', $actions);

    $status = $moderation_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Moderation type.', [
          '%label' => $moderation_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Moderation type.', [
          '%label' => $moderation_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($moderation_type->toUrl('collection'));
  }

}
