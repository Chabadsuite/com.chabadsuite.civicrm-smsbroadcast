<?php
/*
 +--------------------------------------------------------------------+
 | SMS Broadcast                                                      |
 +--------------------------------------------------------------------+
 | Copyright LLC (c) 2018-2019                                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/
/**
 *
 * @package CRM
 * @copyright LLC (c) 2017-2018
 * $Id$
 *
 */
class org_civicrm_smsbroadcast extends CRM_SMS_Provider {

  /**
   * api type to use to send a message
   * @var	string
   */
  protected $_apiType = 'http';

  /**
   * provider details
   * @var	string
   */
  protected $_providerInfo = [];

  /**
   * Curl handle resource id
   *
   */
  protected $_ch;


  public $_apiURL = "https://www.smsbroadcast.com.au/api-adv.php";

   /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = [];

  /**
   * Constructor
   *
   * Create and auth a SMS Broadcast session.
   *
   * @return void
   */
  function __construct($provider = [], $skipAuth = FALSE) {
    // initialize vars
    $this->_apiType = CRM_Utils_Array::value('api_type', $provider, 'http');
    $this->_providerInfo = $provider;

    if ($skipAuth) {
      return TRUE;
    }

    // first create the curl handle
    // Reuse the curl handle
    $this->_ch = curl_init();
    if (!$this->_ch || !is_resource($this->_ch)) {
      return PEAR::raiseError('Cannot initialise a new curl handle.');
    }

    curl_setopt($this->_ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($this->_ch, CURLOPT_VERBOSE, 1);
    curl_setopt($this->_ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($this->_ch, CURLOPT_COOKIEJAR, "/dev/null");
    curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($this->_ch, CURLOPT_USERAGENT, 'CiviCRM - http://civicrm.org/');
  }

   /**
   * singleton function used to manage this object.
   *
   * @return object
   * @static
   *
   */
  public static function &singleton($providerParams = [], $force = FALSE) {
    $providerID = CRM_Utils_Array::value('provider_id', $providerParams);
    $skipAuth   = $providerID ? FALSE : TRUE;
    $cacheKey   = (int) $providerID;

    if (!isset(self::$_singleton[$cacheKey]) || $force) {
      $provider = [];
      if ($providerID) {
        $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
      }
      self::$_singleton[$cacheKey] = new org_civicrm_smsbroadcast($provider, $skipAuth);
    }
    return self::$_singleton[$cacheKey];
  }

  /**
   * Generate Post Data.
   *
   * @param array $header
   * @param string $message
   *
   * @return array
   * @access public
   */
  public function formURLPostData($header, $message) {
    $postDataArray = 'username='.rawurlencode($this->_providerInfo['username']) .
      '&password=' . rawurlencode($this->_providerInfo['password']) .
      '&to=' . rawurlencode($header['To']) .
      '&from=' . rawurlencode($this->_providerInfo['api_params']['from']) .
      '&message=' . rawurlencode($message);
    return $postDataArray;
  }

  /**
   * Send an SMS Message via the API Server.
   *
   * @param array $recipients
   * @param string $header
   * @param string $message
   * @param int $jobID
   */
  public function send($recipients, $header, $message, $jobID = NULL) {
    if ($this->_apiType = 'http') {
      $postDataArray = $this->formURLPostData($header, $message);
      $url = $this->_providerInfo['api_url'];

      $isTest = 0;
      if (array_key_exists('is_test', $this->_providerInfo['api_params']) &&
        $this->_providerInfo['api_params']['is_test'] == 1
      ) {
        $isTest = 1;
      }

      if ($isTest == 1) {
        $responses = ['data' => 'Your message is successfully sent to:' . rand()];
      }
      else {
        $postData = CRM_Utils_System::urlEncode($postDataArray);
        $responses = $this->curl($url, $postData);
      }

      // handle error
      $error = $success = [];
      foreach ($responses as $response) {
        if (!empty($response['success'])) {
          $activity = $this->createActivity($response['data'][2], $message, $header, $jobID);
          $success[] = ts("Successfully delivered to {$header['to']}.");
          if (!empty($header['parent_activity_id'])) {
            civicrm_api3('Activity', 'create', [
                'parent_id' => $header['parent_activity_id'],
                'id' => $activity->id,
              ]
            );
          }
          return TRUE;
        }
        elseif(!empty($response['error'])) {
          if (!empty($header['parent_activity_id'])) {
            civicrm_api3('Activity', 'create', [
                'id' => $header['parent_activity_id'],
                'status_id' => 'Cancelled',
              ]
            );
          }
          return PEAR::raiseError($response['error'], NULL, PEAR_ERROR_RETURN);
        }
      }
    }
  }

  /**
   * Process Inbound sms.
   *
   */
  public function inbound() {
    $like = "";
    $fromPhone = $this->retrieve('from', 'String');
    $fromPhone = $this->formatPhone($this->stripPhone($fromPhone), $like, "like");
    $to = $this->retrieve('to', 'String');
    $to = $this->formatPhone($this->stripPhone($to), $like, "like");
    $message = $this->retrieve('message', 'String');
    $refId = $this->retrieve('ref', 'String');
    return parent::processInbound($fromPhone, $message, $to, $refId);
  }

  /**
   * Perform curl stuff.
   *
   * @param   string  URL to call
   * @param   string  HTTP Post Data
   *
   * @return  mixed   HTTP response body or PEAR Error Object
   * @access	private
   */
  private function curl($url, $postData) {
    $this->_fp = tmpfile();

    curl_setopt($this->_ch, CURLOPT_URL, $url);
    curl_setopt($this->_ch, CURLOPT_POST, TRUE);
    curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);

    $response = curl_exec($this->_ch);
    CRM_Core_Error::debug('s', $response);
    $responses = [];
    $response = explode("\n", $response);
    foreach ($response as $key => $data) {
      $messageData = explode(':', $data);
      $responses[$key]['data'] = $messageData;
      if ($messageData[0] == "OK") {
        $responses[$key]['success'] = TRUE;
      }
      elseif ($messageData[0] == "BAD") {
        $responses[$key]['error'] =  ts("The message to " . $messageData[1] . " was NOT delivered. Reason: " . $messageData[2] . "\n");
      }
      elseif ($messageData[0] == "ERROR") {
        $responses[$key]['error'] = ts("There was an error with this request. Reason: " . $messageData[1] . "\n");
      }
    }
    //$responses = [['success'=>1, 'data'=>['OK', '9665250228','abc123']]];
    return $responses;
  }
}
