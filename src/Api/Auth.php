<?php

namespace Salesforce\Api;

use GuzzleHttp\Client;
use Salesforce\Interfaces\AuthInterface;
use phpDocumentor\Reflection\Types\Boolean;

class Auth extends Base implements AuthInterface
{
  /**
   * @var string
   */
  private $username;
  
  /**
   * @var string
   */
  private $password;
  
  /**
   * @var string
   */
  private $consumerKey;
  
  /**
   * @var string
   */
  private $consumerSecret;
  
  /**
   * @var string
   */
  private $accessToken;
  
  /**
   * @var string
   */
  private $securityToken;
  
  /**
   * @var boolean
   */
  private $isAuthorized = false;
  
  /**
   * @var GuzzleHttp\Client
   */
  protected $guzzle; 
  
  public function __construct (array $params)
  { 
    $this->username = $params['user'];
    $this->password = $params['pass'];
    $this->consumerKey = $params['key'];
    $this->consumerSecret = $params['secret'];
    $this->securityToken = $params['token'];
    $this->baseUrl = array_key_exists('url', $params) ? $params['url'] : $this->baseUrl;
    
    $this->guzzle = new Client(['base_uri' => $this->baseUrl]);
  }
  
  /**
   * Check Authentication Parameters
   * 
   * @todo Build out exception
   * @param type $params
   * @throws \Exception
   */
  public static function checkParams ($params): bool
  {
    $arr = array('user', 'pass', 'key', 'secret', 'token');
    foreach($arr as $a)
    {
      if (!array_key_exists($a, $params)) {
        throw new \Exception('');
      }
    }

    return true;
  }
  
  /**
   * Retrieve authorization access token
   * 
   * @return string
   */
  protected function getAccessToken() : string
  {
    if (null === $this->accessToken) {
      $query = [
        'grant_type'    => 'password',
        'client_id'     => $this->consumerKey,
        'client_secret' => $this->consumerSecret,
        'username'      => $this->username,
        'password'      => $this->password.$this->securityToken,
      ];

      $uri = sprintf('%s?%s', '/services/oauth2/token', http_build_query($query));
      $response = $this->guzzle->request('POST', $uri);

      if (200 == $response->getStatusCode()) {
        $body = json_decode($response->getBody(true), true);
        $this->accessToken = $body['access_token'];
        $this->setIsAuthorized();
      }
    }

    return $this->accessToken;
  }
  
  /**
   * Builds headers for request
   * 
   * @return string
   */
  protected function getHeaders() : string
  {
    $headers = array(
      'content-type' => 'application/json',
      'accept' => 'application/json',
      'authorization' => sprintf('Bearer %s', $this->getAccessToken()),
      'x-prettyprint' => 1,
      'x-sfdc-session' => substr($this->getAccessToken(), strpos($this->getAccessToken(), '!'))
    );
    
    return $headers;
  }
  
  /**
   * Is Authorized
   * 
   * @return boolean
   */
  protected function getIsAuthorized() : bool
  {
    return $this->isAuthorized;
  }
  
  protected function setIsAuthorized() : self
  {
    $this->isAuthorized = true;
    
    return $this;
  }
}

