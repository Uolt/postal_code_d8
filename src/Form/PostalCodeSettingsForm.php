<?php

namespace Drupal\postal_code\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManager;
use Drupal\Component\Utility\Unicode;

/**
 * User expire admin settings form.
 */
class PostalCodeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'postal_code_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['postal_code.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $country_list = CountryManager::getStandardList();
    $config = $this->config('postal_code.settings');
    $postal_code_validation_data = postal_code_validation();
    $options = array();

    foreach ($postal_code_validation_data as $country_code => $info) {
      $options[$country_code] = $country_list[Unicode::strtoupper($country_code)];
    }

    $form['postal_code_valid_countries'] = array(
      '#type'           => 'select',
      '#title'          => t('Valid "Any" Countries'),
      '#size'           => 16,
      '#multiple'       => TRUE,
      '#options'        => $options,
      '#default_value'  => $config->get('valid_countries'),
      '#description'    => '<p>' . t('Select the countr(y/ies) for Postal Code Validation for "Any" field type.') . '</p><p><em>' . t('This is most useful when you have a form that allows, for example, US and Canadian addresses.') . '</em></p><p><strong>' . t('VALIDATION ONLY OCCURS IF THE "VALIDATE" CHECKBOX BELOW IS SELECTED.') . '</strong></p>',
    );

    $form['postal_code_validate'] = array(
      '#type'           => 'checkbox',
      '#title'          => t('Validate'),
      '#default_value'  => $config->get('validate'),
      '#description'    => t('Validate submitted postal codes?'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('postal_code.settings');

    // Write settings into config file.
    $config
      ->set('valid_countries', $form_state->getValue('postal_code_valid_countries'))
      ->set('validate', $form_state->getValue('postal_code_validate'))
      ->save();
  }
}