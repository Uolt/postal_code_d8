<?php

namespace Drupal\postal_code\Plugin\migrate\process\d6;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @MigrateProcessPlugin(
 *   id = "d6_cck_zipcode"
 * )
 */
class CckPostalcode extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->migration = $migration;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration
    );
  }

  /**
   * Flag indicating whether there are multiple values.
   *
   * @var bool
   */
  protected $multiple;

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $source = $this->configuration['source'];
    $properties = is_string($source) ? array($source) : $source;
    $return = array();
    foreach ($properties as $property) {
      if ($property || (string) $property === '0') {
        $is_source = TRUE;
        if ($property[0] == '@') {
          $property = preg_replace_callback('/^(@?)((?:@@)*)([^@]|$)/', function ($matches) use (&$is_source) {
            // If there are an odd number of @ in the beginning, it's a
            // destination.
            $is_source = empty($matches[1]);
            // Remove the possible escaping and do not lose the terminating
            // non-@ either.
            return str_replace('@@', '@', $matches[2]) . $matches[3];
          }, $property);
        }
        if ($is_source) {
          $return[] = $row->getSourceProperty($property);
        }
        else {
          $return[] = $row->getDestinationProperty($property);
        }
      }
      else {
        $return[] = $value;
      }
    }

    if (is_string($source)) {
      $this->multiple = is_array($return[0]);
      return $return[0];
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return $this->multiple;
  }

}
