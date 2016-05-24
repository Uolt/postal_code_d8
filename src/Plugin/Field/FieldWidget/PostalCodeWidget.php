<?php

namespace Drupal\postal_code\Plugin\Field\FieldWidget;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'postal_code' widget.
 *
 * @FieldWidget(
 *   id = "postal_code_any_postal_code_form",
 *   module = "postal_code",
 *   label = @Translation("Postal Code: Any Format"),
 *   field_types = {
 *     "postal_code"
 *   }
 * )
 */
class PostalCodeWidget extends WidgetBase {
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $element += array(
      '#type' => 'textfield',
      '#default_value' => $value,
      '#size' => 16,
      '#maxlength' => 16,
      '#element_validate' => array(
        array($this, 'validate'),
      ),
      '#description' => $this->t('Select country for validation'),
    );
    return array('value' => $element);
  }

  /**
   * Validate the postal code field.
   */
  public function validate($element, FormStateInterface $form_state) {
    $field_settings = $this->getFieldSettings();
    $config = \Drupal::configFactory()->getEditable('postal_code.settings');
    $value = trim($element['#value']);

    if (!empty($value) && $config->get('validate')) {
      // Locate 'postal_type' in the form.
      $country_code = $field_settings['country_select'];

      if (!empty($country_code)) {
        if ($country_code != 'any') {
          $error_array = _postal_code_validator($country_code, $value);
        }
        else {
          $validatable_countries = $config->get('valid_countries');
          foreach ($validatable_countries as $key => $country) {
            $err_array[] = _postal_code_validator($country, $value);
          }
          foreach ($err_array as $k => $v) {
            $error_array[] = $v[0];
          }
        }
      }
      else {
        $form_state->setError($element, $this->t('This form has been altered in a way in which Postal Code validation will not work, but the validation option remains enabled. Please correct the changes to the form or disable the validation option.'));
      }

      if (!in_array(TRUE, $error_array)) {
        $form_state->setError($element, $this->t('Invalid Postal Code Provided.'));
      }
    }
  }
}