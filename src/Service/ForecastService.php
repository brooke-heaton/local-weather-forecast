<?php

/**
 * @file
 * Contains \Drupal\local_weather_forecast\Controller\WeatherController.
 */

namespace Drupal\local_weather_forecast\Service;

use \GuzzleHttp\ClientInterface;

class ForecastService {

  protected $client;

  protected $api_key;

  /**
   * ForecastService constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   */
  function __construct(ClientInterface $client) {
    $this->client = $client;
    $this->api_key = $this->getApiKey();
  }

  /**
   * {@inheritdoc}
   */
  public function getApiKey() {
    $config = \Drupal::config('local_weather_forecast.settings');
    return $config->get('api_key');
  }

  /**
   *
   * @param string $city
   *
   * @param int $cnt
   *
   * @return array $element
   */
  public function getByCity($city, $cnt) {
    // get data via http://openweathermap.org/api
    /* @var \GuzzleHttp\Message\Response $result */
    $request = $this->client->get(
      'https://api.openweathermap.org/data/2.5/forecast',
      [
        'query' => [
          'q' => $city . ",us",
          'appid' => $this->api_key,
          'cnt' => $cnt,
        ],
      ]
    );
    try {
      if (200 == $request->getStatusCode()) {
        $forecast = json_decode($request->getBody());
      }
      $hourly_forecast = $forecast->list;
      // use our theme function to render twig template
      $element = [
        '#theme' => 'local_weather_forecast',
        '#city' => $forecast->city->name,
        '#forecast' => $hourly_forecast,
      ];
      $element['#cache']['max-age'] = 0;
      return $element;
    } catch (\Exception $e) {
      drupal_set_message(t("Could not get a forecast for $city, please try again later:" . $e->getMessage()), 'error');
    }
  }
}
