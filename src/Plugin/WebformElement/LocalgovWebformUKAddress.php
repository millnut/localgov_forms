<?php

namespace Drupal\localgov_forms\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'localgov_webform_uk_address' Webform element.
 *
 * @WebformElement(
 *   id = "localgov_webform_uk_address",
 *   label = @Translation("Localgov address lookup"),
 *   description = @Translation("Provides a UK address lookup element."),
 *   category = @Translation("Composite elements"),
 *   multiline = TRUE,
 *   composite = TRUE,
 *   states_wrapper = TRUE,
 * )
 *
 * @see \Drupal\webform_example_composite\Element\WebformExampleComposite
 * @see \Drupal\webform\Plugin\WebformElement\WebformCompositeBase
 * @see \Drupal\webform\Plugin\WebformElementBase
 * @see \Drupal\webform\Plugin\WebformElementInterface
 * @see \Drupal\webform\Annotation\WebformElement
 */
class LocalgovWebformUKAddress extends WebformUKAddress {

  /**
   * Declares our properties.
   *
   * Configurable properties:
   * - geocoder_plugins
   * - always_display_manual_address_entry_btn.
   *
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {

    $parent_properties = parent::defineDefaultProperties();
    $parent_properties['geocoder_plugins'] = [];
    $parent_properties['always_display_manual_address_entry_btn'] = 'yes';

    return $parent_properties;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(array &$element, WebformSubmissionInterface $webform_submission) {
    $submission_data = $webform_submission->getData();
    $webform = $webform_submission->getWebform();
    foreach ($submission_data as $key => $value) {
      $webform_element = $webform->getElement($key);
      if ($webform_element['#type'] == 'localgov_webform_uk_address') {
        unset($submission_data[$key]['address_lookup']);
        $extra_elements = ['lat', 'lng', 'ward'];
        foreach ($extra_elements as $extra_element) {
          unset($submission_data[$extra_element]);
        }
      }
    }
    $webform_submission->setData($submission_data);
  }

  /**
   * Webform element config form.
   *
   * Adds settings for:
   * - Selecting Geocoder plugins for address lookup.
   * - Deciding whether to display the manual address entry button at all times.
   *
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $parent_form = parent::form($form, $form_state);

    $parent_form['element']['geocoder_plugins'] = [
      '#type'     => 'checkboxes',
      '#title'    => $this->t('Geocoder plugins'),
      '#options'  => \Drupal::service('localgov_forms.geocoder_selection')->listInstalledPluginNames(),
      '#required' => TRUE,
      '#description' => $this->t('These plugins are used for address lookup.  They are added from Configuration > System > Geocoder > Providers.'),
    ];

    $parent_form['element']['always_display_manual_address_entry_btn'] = [
      '#type'        => 'radios',
      '#title'       => 'When to display the manual address entry button',
      '#description'   => $this->t('Either display at all times or only after an address search.'),
      '#options'     => [
        'yes' => $this->t('Always'),
        'no'  => $this->t('After an address search'),
      ],
    ];

    return $parent_form;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatHtmlItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    return $this->formatTextItemValue($element, $webform_submission, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function formatTextItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);

    $lines = [];
    $lines[] =
      ($value['address_1'] ? $value['address_1'] : '') .
      ($value['address_2'] ? ' ' . $value['address_2'] : '') .
      ($value['town_city'] ? ' ' . $value['town_city'] : '') .
      ($value['postcode'] ? ' ' . $value['postcode'] : '');
    return $lines;
  }

}
