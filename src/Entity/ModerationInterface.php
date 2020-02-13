<?php

namespace Drupal\moderation\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Moderation entities.
 *
 * @ingroup moderation
 */
interface ModerationInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Moderation name.
   *
   * @return string
   *   Name of the Moderation.
   */
  public function getName();

  /**
   * Sets the Moderation name.
   *
   * @param string $name
   *   The Moderation name.
   *
   * @return \Drupal\moderation\Entity\ModerationInterface
   *   The called Moderation entity.
   */
  public function setName($name);

  /**
   * Gets the Moderation creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Moderation.
   */
  public function getCreatedTime();

  /**
   * Sets the Moderation creation timestamp.
   *
   * @param int $timestamp
   *   The Moderation creation timestamp.
   *
   * @return \Drupal\moderation\Entity\ModerationInterface
   *   The called Moderation entity.
   */
  public function setCreatedTime($timestamp);

  public function getData();

  public function getDataValue($key);

}
