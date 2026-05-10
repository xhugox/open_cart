<?php

namespace unisend_shipping\api;

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\services\UnisendShippingCacheService;
use unisend_shipping\services\UnisendShippingConfigService;
use UnisendRequestExecutor;
use UnisendShippingRequestErrorHandler;

require_once(dirname(__FILE__) . '/UnisendRequestExecutor.php');
require_once(dirname(__FILE__) . '/UnisendShippingRequestErrorHandler.php');

/**
 * Singleton class to make calls to API
 */
class UnisendApi extends UnisendRequestExecutor
{
    /**
     * Lietuvos Pastas API version
     */
    const API_VERSION = 'api/v2/';
    const DEFAULT_ACCEPT = 'application/json';
    const DEFAULT_TIMEOUT = 30;

    private static $instance = null;

    private static $baseUrl;

    private $token = null;

    /** @var UnisendShippingRequestErrorHandler */
    private $errorHandler = null;

    public function __construct()
    {
        parent::__construct();
        $this->errorHandler = UnisendShippingRequestErrorHandler::getInstance();
        $this->setApiUrl();
    }

    /**
     * Check if module working mode is live
     *
     * @return bool
     */
    protected function isLiveMode()
    {
        return true;//UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_MODE_LIVE);
    }

    /**
     * Set base url of API
     */
    public function setApiUrl($live = null)
    {
        $live = true;//$live !== null ? $live : UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_MODE_LIVE);
        if ($live) {
            self::$baseUrl = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_API_URL);
        } else {
            self::$baseUrl = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_API_TEST_URL);
        }
    }

    public function isAuthenticated()
    {
        $dbToken = $this->getSavedToken();
        if (is_array($dbToken) && count($dbToken) > 0) {
            return $dbToken['expiration_date'] > time();
        }
        return false;
    }

    public function doTokenExists()
    {
        $dbToken = $this->getSavedToken();
        return is_array($dbToken) && count($dbToken) > 0;
    }

    /**
     * Check if result contains error
     *
     * @param array $result
     *
     * @return bool|string
     */
    private function isError(array $result)
    {
        if (is_array($result) && key_exists('error', $result)) {
            return $result['error_description'];
        }

        return false;
    }

    /**
     * Build authentication header
     *
     * @return array
     */
    private function buildAuthHeader($token)
    {
        return [
            'Authorization: Bearer ' . $token,
        ];
    }

    private function getSavedToken()
    {
        return UnisendShippingCacheService::get(UnisendShippingConst::SETTING_KEY_API_TOKEN) ?? unserialize(UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_API_TOKEN));
    }

    /**
     * Get API token from DB or from instance, depends if token has been retrieved already
     *
     * @return string
     */
    private function getApiToken()
    {
        $dbToken = $this->getSavedToken();
        if (is_array($dbToken) && count($dbToken) > 0) {
            if (time() >= $dbToken['expiration_date']) {
                if (!$this->refreshToken()) {
                    return '';
                }

                $dbToken = $this->getSavedToken();

                return $dbToken['access_token'];
            }

            $this->token = $dbToken['access_token'];

            return $dbToken['access_token'];
        } else {
            if (!$this->authenticate()) {
                return '';
            }

            $dbToken = $this->getSavedToken();

            return $dbToken['access_token'];
        }

        return '';
    }

    /**
     * Authenticate to API and retrieve token
     *
     * @return bool
     */
    public function authenticate($username = '', $password = '')
    {
        $username = $username !== '' ? $username : UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_USERNAME) ?? null;
        $password = $password !== '' ? $password : UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_PASSWORD) ?? null;

        if (!$username || !$password) {
            return false;
        }

        $authQuery = http_build_query([
            'username' => $username,
            'password' => $password,
            'grant_type' => 'password',
            'scope' => 'read+write API_CLIENT',
            'clientSystem' => 'PUBLIC'
        ]);

        $endpoint = self::$baseUrl . 'oauth/token?' . $authQuery;

        $requestOptions = [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ];

        $this->setHeaders([]); // sets default headers
        $this->setOptions($requestOptions);

        $res = $this->executeCallAndGetResult();
        if (!$this->errorHandler->isRequestCompletedSuccessfully($res)) {
            $this->deleteToken();
            return false;
        }

        $result = json_decode($res, true);

        $this->refreshCurl();

        return $this->handleAuthResponse($result);
    }

    private function handleAuthResponse($response)
    {
        if (is_array($response) && key_exists('access_token', $response) && !empty($response['access_token'])) {
            $response['expiration_date'] = time() + ((int)$response['expires_in']);

            $this->token = $response['access_token'];
            $this->saveToken($response);

            return true;
        }
        return false;
    }

    private function saveToken($response)
    {
        UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_API_TOKEN, serialize($response)); // Save whole token to DB with expiration time
        UnisendShippingCacheService::save(UnisendShippingConst::SETTING_KEY_API_TOKEN, $response, (int)$response['expires_in'] + (60 * 60));// prolong for refresh token
    }

    private function deleteToken()
    {
        UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_API_TOKEN, '');
        UnisendShippingCacheService::delete(UnisendShippingConst::SETTING_KEY_API_TOKEN);
    }

    /**
     * Refresh API token
     *
     * @return bool
     */
    private function refreshToken()
    {
        $tokenData = $this->getSavedToken();

        $authQuery = http_build_query([
            'grant_type' => 'refresh_token',
            'refresh_token' => $tokenData['refresh_token']
        ]);

        $endpoint = self::$baseUrl . 'oauth/token?' . $authQuery;

        $requestOptions = [
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
        ];

        $this->setHeaders([]); // sets default headers
        $this->setOptions($requestOptions);

        $res = $this->executeCallAndGetResult();
        if (!$this->errorHandler->isRequestCompletedSuccessfully($res)) {
            $this->deleteToken();
            return false;
        }

        $result = json_decode($res, true);

        $this->refreshCurl();

        return $this->handleAuthResponse($result);
    }

    public function post($endpoint, $body = [], callable $error_callback = null)
    {
        return self::request($endpoint, null, $body, 'POST', true, self::DEFAULT_ACCEPT, $error_callback);
    }

    public function get($endpoint, $params = [], string $accept = self::DEFAULT_ACCEPT, int $timeout = self::DEFAULT_TIMEOUT)
    {
        return self::request($endpoint, $params, null, 'GET', true, $accept, null, $timeout);
    }

    public function put($endpoint, $body = [], callable $error_callback = null)
    {
        return self::request($endpoint, null, $body, 'PUT', true, self::DEFAULT_ACCEPT, $error_callback);
    }

    public function delete($endpoint, $params = [])
    {
        return self::request($endpoint, $params, null, 'DELETE', true);
    }

    protected function isAuthRequired()
    {
        return true;
    }

    public function request($uri, $params = [], $body = [], $method = 'GET', $retry_on_unauthorized_request = false, $accept = self::DEFAULT_ACCEPT, callable $error_callback = null, $timeout = self::DEFAULT_TIMEOUT)
    {
        $headers = ['Accept' => $accept];
        if ($this->isAuthRequired()) {
            $authToken = $this->getApiToken();
            if (!$authToken) return [
                'success' => false,
                'status_code' => 401,
                'message' => 'Bad credentials'
            ];
            $headers = array_merge($this->buildAuthHeader($authToken), $headers);
        }


        $uri_param = null;
        if ($params && count($params) > 0) {
            $uri_param = http_build_query($params, '', '&');
        }
        $endpoint = self::$baseUrl . self::API_VERSION . $uri . ($uri_param ? ("?" . $uri_param) : null);
        $requestOptions = [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body ? json_encode($body) : null,
            CURLOPT_TIMEOUT => $timeout,
        ];

        $this->setHeaders($headers);
        $this->setOptions($requestOptions);

        $res = $this->executeCallAndGetResult();
        if (!$this->errorHandler->isRequestCompletedSuccessfully($res)) {
            if ($res['status_code'] === 401 && $this->isAuthRequired()) {
                $this->deleteToken();
                if ($retry_on_unauthorized_request) {
                    if ($this->refreshToken()) {
                        return $this->request($uri, $params, $body, $method, false, $accept, $error_callback, $timeout);
                    }
                }
            }
            return $res;
        }

        if ($accept === self::DEFAULT_ACCEPT) {
            $result = json_decode($res, $this->isResponseAsArray());
        } else {
            $result = $res;
        }

        $this->refreshCurl();

        if ($result && is_array($result) && $err = $this->isError($result)) {
//            throw new Exception($err);//TODO implement
        }
        if ($this->isResponseAsArray()) return $result;
        return $this->toObject($result);
    }

    private function toObject($arr)
    {
        if (is_array($arr)) {
            return (object)array_map([$this, 'toObject'], $arr);
        }
        return $arr;
    }

    public function isResponseAsArray(): bool
    {
        return false;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendApi();
        }
        return self::$instance;
    }
}
