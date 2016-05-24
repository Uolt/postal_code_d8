<?php

namespace Drupal\postal_code\Plugin\migrate\destination;


use Drupal\migrate\Plugin\migrate\destination\EntityFieldInstance;

/**
 * Provides entity field instance plugin.
 *
 * @MigrateDestination(
 *   id = "entity:postal_code"
 * )
 */
class PostalcodeFieldInstance extends EntityFieldInstance {

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['entity_type']['type'] = 'string';
    $ids['bundle']['type'] = 'postal_code';
    $ids['field_name']['type'] = 'postal_code';
    return $ids;
  }

}