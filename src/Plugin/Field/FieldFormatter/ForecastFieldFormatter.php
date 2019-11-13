<?php

namespace Drupal\local_weather_forecast\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'forecast_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "forecast_field_formatter",
 *   label = @Translation("Forecast field formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class ForecastFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = [];
    $settings['increments'] = 40;
    $settings += parent::defaultSettings();
    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getSettings();

    $element['increments'] = [
      '#type' => 'number',
      '#title' => $this->t('Forecast Increments'),
      '#description' => $this->t('Up to 40 forecast increments may be displayed at 3 hour intervals'),
      '#default_value' => $settings['increments'],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = t('Dipslays a weather forecast for a given city name.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    // Limit the number of returned results to the number of increments
    $increments =  $this->getSettings('increments')['increments'];
    foreach ($items as $delta => $item) {
      if($city = $item->entity->getName()) {
          $forecaster = \Drupal::service('local_weather_forecast.forecast');
          $elements[$delta] = $forecaster->getByCity($city, $increments);
        }
    }
    foreach ($elements as &$element) {
      $element['#theme'] = 'local_weather_forecast';
    }
    foreach ($elements as &$element) {
      $element['#increments'] = $increments;
    }


    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }


}
