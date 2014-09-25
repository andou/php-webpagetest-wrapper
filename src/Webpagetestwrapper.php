<?php

namespace Andou\Webpagetestwrapper;

use Andou\Api\Api;

/**
 * Your own personal Api Fetcher.
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) 2014 Antonio Pastorino <antonio.pastorino@gmail.com>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @category apitool
 * @package andou/apitool
 * @copyright MIT License (http://opensource.org/licenses/MIT)
 */
class Webpagetestwrapper {

  /**
   * Api call
   *
   * @var Andou\Api\Api
   */
  protected $_api;

  /**
   * Api to make calls
   *
   * @var string
   */
  protected $_api_key;

  /**
   * The requested format for the response
   *
   * @var string
   */
  protected $_return_format = 'json';

  /**
   * 
   *
   * @var string
   */
  protected $_wpt_b_url = 'http://www.webpagetest.org/';

  /**
   * url to run tests
   *
   * @var string 
   */
  protected $_runtest_url = 'runtest.php';

  /**
   * test status url
   *
   * @var string 
   */
  protected $_teststatus_url = 'testStatus.php';

  /**
   * test results url
   *
   * @var string 
   */
  protected $_testrestults_url = 'jsonResult.php';

  /**
   * Location for the test
   *
   * @var string
   */
  protected $_location;

  /**
   * Browser to check
   *
   * @var string
   */
  protected $_browser;

  /**
   * Connectivity type
   *
   * @var string 
   */
  protected $_connectivity;

  /**
   * Returns an instance of this class
   *
   * @param string $api_key 
   * @return \Andou\Webpagetestwrapper
   */
  public static function getInstance($api_key) {
    $classname = __CLASS__;
    return new $classname($api_key);
  }

  /**
   * Class constructor
   * 
   * @param string $api_key
   */
  public function __construct($api_key) {
    require_once dirname(__FILE__) . '/definitions.php';
    $this->_api_key = $api_key;
    $this->_api = Api::getInstance();
  }

  /**
   * Sets a location from where to test the page
   * 
   * @param string $_location
   */
  public function setLocation($_location) {
    $this->_location = $_location;
  }

  /**
   * Sets a browser to run the test
   * 
   * @param type $_browser
   */
  public function setBrowser($_browser) {
    $this->_browser = $_browser;
  }

  /**
   * Sets the connectivity type
   * 
   * @param type $_connectivity
   */
  public function setConnectivity($_connectivity) {
    $this->_connectivity = $_connectivity;
  }

  /**
   * Schedules a test
   * 
   * @param string $url_to_test
   * @return boolean|string
   */
  public function runTest($url_to_test = FALSE) {
    if (!$url_to_test) {
      return FALSE;
    }
    $data = $this->_getApi()
            ->setApiAddress($this->_wpt_b_url . $this->_runtest_url)
            ->apiCall($this->_populateRunTestArguments($url_to_test));
    return $data;
  }

  /**
   * Checks for the status of the test
   * 
   * @param string $test_id
   * @return boolean|string
   */
  public function testStatus($test_id = FALSE) {
    if (!$test_id) {
      return FALSE;
    }
    $data = $this->_getApi()
            ->setApiAddress($this->_wpt_b_url . $this->_teststatus_url)
            ->apiCall($this->_populateTestStatusArguments($test_id));
    return $data;
  }

  /**
   * Retrieves the results of a test
   * 
   * @param string $test_id
   * @return boolean|string
   */
  public function testResults($test_id) {
    if (!$test_id) {
      return FALSE;
    }
    $data = $this->_getApi()
            ->setApiAddress($this->_wpt_b_url . $this->_teststatus_url)
            ->apiCall($this->_populateResultsArguments($test_id));
    return $data;
  }

  /**
   * Returns an instance of API to make calls
   * 
   * @return \Andou\Api\Api
   */
  protected function _getApi() {
    return $this->_api;
  }

  /**
   * Populate arguments to run tests
   * 
   * @param string $url_to_test
   * @return array
   */
  protected function _populateRunTestArguments($url_to_test) {
    $res = array();

    if (isset($this->_api_key)) {
      $res[WPT_PARAMETER_KEY] = $this->_api_key;
    }

    if (isset($this->_return_format)) {
      $res[WPT_PARAMETER_FORMAT] = $this->_return_format;
    }

    $location = $this->_composeLocation();
    if ($location != "") {
      $res[WPT_PARAMETER_LOCATION] = $location;
    }

    $res[WPT_PARAMETER_URL] = $url_to_test;

    return $res;
  }

  /**
   * Populate arguments to check test status
   * 
   * @param string $test_id
   * @return array
   */
  protected function _populateTestStatusArguments($test_id) {
    $res = array();
    if (isset($this->_api_key)) {
      $res[WPT_PARAMETER_KEY] = $this->_api_key;
    }

    if (isset($this->_return_format)) {
      $res[WPT_PARAMETER_FORMAT] = $this->_return_format;
    }

    $res[WPT_PARAMETER_TESTID] = $test_id;

    return $res;
  }

  /**
   * Populate arguments to retrieve test results
   * 
   * @param string $test_id
   * @return array
   */
  protected function _populateResultsArguments($test_id) {
    $res = array();
    if (isset($this->_api_key)) {
      $res[WPT_PARAMETER_KEY] = $this->_api_key;
    }

    $res[WPT_PARAMETER_TESTID] = $test_id;

    return $res;
  }

  /**
   * Compose a location for a run test request
   * 
   * @return string
   */
  protected function _composeLocation() {
    $loc = isset($this->_location) ? sprintf("%s", $this->_location) : "";
    $brw = isset($this->_browser) ? sprintf(":%s", $this->_browser) : "";
    $con = isset($this->_connectivity) ? sprintf(".%s", $this->_connectivity) : "";
    return sprintf("%s%s%s", $loc, $brw, $con);
  }

}