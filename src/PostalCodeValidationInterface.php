<?php

namespace Drupal\postal_code;


/**
 * Interface ValidatorServiceInterface.
 * Provide interface with additional methods for validations of postal code field.
 */
interface PostalCodeValidationInterface {

  /**
   * Actual validation function.
   *
   * @return array of RegExp patterns for validation.
   */
  public function getValidationPatterns();

  /**
   * Custom function defining regexes corresponding to different countries.
   *
   * @param $countrycode
   * @param $formvalue
   * @return mixed
   */
  public function validate($countrycode, $formvalue);
}
