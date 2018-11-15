<?php

namespace Salesforce\Api;

class Base
{
  protected $apiVersion = 'v43.0';

  protected $baseUri = '/services/data';
  
  protected $baseUrl = 'https://salesforce.com';

  public function getApiVersion ()  
  {
    return $this->apiVersion;
  }
  
  /**
   * Override API Version
   * 
   * @param \Salesforce\String $version
   * @return \self
   */
  public function setApiVersion (String $version) 
  {
    $this->apiVersion = $version;

    return $this;
  }
  
  /**
   * Request Uri Base for API Services
   * 
   * @return string
   */
  public function getBaseUri () 
  {
    return $this->baseUri;
  }

  /**
   * Overrides the Base Uri for API Services
   * 
   * @param \Salesforce\String $uri
   * @return \self
   */
  public function setBaseUri (String $uri) 
  {
    $this->baseUri = $uri;

    return $this;
  }
  
  /**
   * Request Base Url
   * 
   * @return string
   */
  public function getBaseUrl () 
  {
    return $this->baseUrl;
  }
  
  /**
   * Override Salesforce Base Url
   * 
   * @param \Salesforce\String $url
   * @return \self
   */
  public function setBaseUrl (String $url) 
  {
    $this->baseUrl = $url;
    
    return $this;
  }
}
