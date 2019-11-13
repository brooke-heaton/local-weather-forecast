<?php

namespace Drupal\Tests\local_weather_forecast\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group local_weather_forecast
 */
class SettingsTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['local_weather_forecast'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($this->user);
  }

  /**
   * Tests the config form.
   */
  public function testConfigForm() {
    // Login.
    $this->drupalLogin($this->user);

    // Access config page.
    $this->drupalGet('admin/config/system/local-weather-forecast');
    $this->assertSession()->statusCodeEquals(200);
    // Test the form elements exist and have defaults.
    $config = $this->config('local_weather_forecast.settings');
    $this->assertSession()->fieldValueEquals(
      'page_title',
      $config->get('/admin/config/system/local-weather-forecast.page_title')
    );
  }

}
