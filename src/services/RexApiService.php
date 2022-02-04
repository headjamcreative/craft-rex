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
use headjam\craftrex\models\RexListingModel;

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
 * @since     1.0.2
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
  public function rexAuthenticatedRequest(string $method, string $endpoint, ?array $postBody)
  {
    $token = CraftRex::getInstance()->rexAuthToken;
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

  /**
   * Query all the published listings on the REX service.
   * @param int $limit - The limit to use in the query. Defaults to 100.
   * @param int $offset - The offset to use in the query. Defaults to 0.
   * @param bool $all - If true, function will be called recursively until all results are obtained. Defaults to false.
   * @param array $accumulator - The array to gather results in.
   */
  public function findAll(?int $limit=100, ?int $offset=0, ?bool $all=false, ?array $accumulator=[])
  {
    $result = $this->rexAuthenticatedRequest('POST', 'published-listings/search', [
      'limit' => $limit,
      'offset' =>  $offset,
      'order_by' => [ 'system_modtime' => 'DESC' ],
      'result_format' => 'website_overrides_applied',
      'extra_options' => [
        'extra_fields' => ['advert_internet', 'images', 'subcategories', 'features', 'events', 'links']
      ]
    ]);
    if ($result['success'] && $result['data']['result']) {
      foreach ($result['data']['result']['rows'] as $entry) {
        $model = RexListingModel::create($entry);
        $accumulator[] = $model;
      }
      if ($all && ($limit + $offset < $result['data']['result']['total'])) {
        return $this->findAll($limit, $limit + $offset, $all, $accumulator);
      }
    }
    return $accumulator;
  }

  /**
   * Query the specific published listings on the REX service.
   * @param int $listingId - The ID to query REX by.
   */
  public function findById(int $listingId)
  {
    $result = $this->rexAuthenticatedRequest('POST', 'published-listings/read', [
      'id' => $listingId,
      'extra_fields' => ['advert_internet', 'images', 'subcategories', 'features', 'events', 'links'],
      'result_format' => 'website_overrides_applied'
    ]);
    if ($result['success'] && $result['data']['result']) {
      return RexListingModel::create($result['data']['result']);
    }
    return null;
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
  private function rexLogin()
  {
    $auth = [
      'email' => CraftRex::getInstance()->getSettings()->getRexUsername(),
      'password' => CraftRex::getInstance()->getSettings()->getRexPassword()
    ];
    $response = $this->rexRequest('POST', $this->authEndpoint, $auth, null);
    if ($response['success'] &&  $response['data']['result']) {
      $token = $response['data']['result'];
      CraftRex::getInstance()->rexAuthToken = $token;
      return $token;
    }
    return false;
  }
}
