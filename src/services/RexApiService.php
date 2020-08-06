<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex\services;

use headjam\craftrex\CraftRex;

use Craft;
use craft\base\Component;
use craft\services\Plugins;

/**
 * REX Api Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.0.0
 */
class RexApiService extends Component
{
  // Private properties
  private $authEndpoint = 'Authentication/login';



  // Public Methods
  // =========================================================================
  /** 
   * Make an authenticated REX api request.
   * @param string $method - The method to query.
   * @param string $endpoint - The endpoint to query.
   * @param array [$postBody] - Any data to submit with the query.
   * @return array An array containing a status and either error or data properties.
   */
  public function rexAuthenticatedRequest(string $method, string $endpoint, ?array $postBody) {
    $token = CraftRex::getInstance()->getSettings()->rexAuthToken;
    if (!(isset($token) && $token !== '')) {
      $token = $this->rexLogin();
    }
    if ($token) {
      return $this->rexRequest($method, $endpoint, $postBody, $token);
    }
    return [
      'success' => false,
      'error' => 'Could not authenticate request'
    ];
  }



  // Private Methods
  // =========================================================================
  /** 
   * Format a REX api request.
   * @param string $method - The method to query.
   * @param string $endpoint - The endpoint to query.
   * @param array [$postBody] - Any data to submit with the query.
   * @return array An array containing a status and either error or data properties.
   */
  private function rexRequest(string $method, string $endpoint, ?array $postBody, ?string $token)
  {
    try {
      $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.rexsoftware.com/v1/rex/']);
      $postData = [
        'http_errors' => false,
        'headers' => ['Content-Type' => 'application/json']
      ];
      if (isset($token) && $token !== '') {
        $postData['headers']['Authorization'] = "Bearer {$token}";
      }
      if (isset($postBody)) {
        $postData['body'] = \GuzzleHttp\json_encode($postBody);
      }
      $response = $client->request($method, $endpoint, $postData);
      if ($response->getStatusCode() === 200) {
        $body = $response->getBody()->getContents();
        return [
          'success' => true,
          'data' => \GuzzleHttp\json_decode($body, true)
        ];
      } elseif ($response->getStatusCode() === 401 && $endpoint !== $this->authEndpoint) {
        $newToken = $this->rexLogin();
        if ($newToken) {
          return $this->rexAuthenticatedRequest($method, $endpoint, $postBody, $newToken);
        }
      }
      return [
        'success' => false,
        'error' => 'Server Error'
      ];
    } catch (Exception $error) {
      return [
        'success' => false,
        'error' => $error->getMessage()
      ];
    }
  }

  /**
   * Login to the REX system to query data.
   * @return string The auth token.
   */
  private function rexLogin() {
    $auth = [
      'email' => CraftRex::getInstance()->getSettings()->getRexUsername(),
      'password' => CraftRex::getInstance()->getSettings()->getRexPassword(),
      'token_lifetime' => 5
    ];
    $response = $this->rexRequest('POST', $this->authEndpoint, $auth, null);
    if ($response['success'] && $response['data'] && $response['data']['result']) {
      $token = $response['data']['result'];
      $plugins = new Plugins();
      $plugins->savePluginSettings(CraftRex::getInstance(), ['rexAuthToken' => $token]);
      return $token;
    }
    return false;
  }
}
