<?php

namespace Drupal\local_weather_forecast\Tests;

use Drupal\local_weather_forecast\WebTestBase;

/**
 * Tests for the local_weather_forecast module.
 *
 * @group Local Weather Forecast
 */
class LocalWeatherForecastTest extends WebTestBase {

  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['local_weather_forecast'];

  /**
   * {@inheritdoc}
   */
  private $user;

  /**
   * {@inheritdoc}
   */
  public function setUp() {

    parent::setUp();
    $this->user = $this->DrupalCreateUser([
      'administer site configuration',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function testMobileJsRedirectPageExists() {

    $this->drupalLogin($this->user);

    // Generator test:
    $this->drupalGet('admin/config/system/forecast');
    $this->assertResponse(200);
  }

  /**
   * {@inheritdoc}
   */
  public function testConfigForm() {

    // Test form structure.
    $this->drupalLogin($this->user);
    $this->drupalGet('admin/config/system/forecast');
    $this->assertResponse(200);
    $config = $this->config('local_weather_forecast.settings');
    $this->assertFieldByName(
      'cache',
      $config->get('cache'),
      'Cache field has the default value'
    );

    $this->drupalPostForm(NULL, [
      'cache' => FALSE,
    ], t('Save configuration'));

    $this->drupalGet('admin/config/system/forecast');
    $this->assertResponse(200);
    $this->assertFieldByName(
      'cache',
      TRUE,
      'Cahe field is OK.'
    );
  }

}
