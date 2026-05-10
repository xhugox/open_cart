<?php

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\services\UnisendShippingConfigService;

class UnisendShippingRequestErrorHandler
{
    private static $instance = null;

    public function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingRequestErrorHandler();
        }

        return self::$instance;
    }

    /**
     * Check if request does have success message false
     *
     * @return bool
     */
    public function isRequestCompletedSuccessfully($result)
    {

        $errorMessage = $this->toErrorMessage($result);
        if ($errorMessage) {
            $this->updateLastError($errorMessage);
            return false;
        }
        return true;
    }


    public function getLastError()
    {
        $err = UnisendShippingConfigService::get($this->getKey());
        if (!empty($err)) {
            $this->updateLastError('');
            return unserialize($err);
        }
        return $err;
    }

    public function updateLastError($message)
    {
        $key = $this->getKey();
        $code = $this->getCode($key);
        if (!$message) {
            UnisendShippingConfigService::deleteAllByCode($code);
            return;
        }
        $resultMessage['message'] = $message;
        UnisendShippingConfigService::updateValue($code, serialize($resultMessage), $key);
    }

    public function getCode($key)
    {
        return substr($key, 0, 32);
    }

    public function toErrorMessage($result)
    {
        if (is_array($result) && array_key_exists('success', $result) && $result['success'] == false) {
            $errors = [];
            $messages = json_decode($result['message'], true);
            if (is_array($messages) && !isset($messages['error'])) {
                if (count($messages) === 0) {
                    return "Request failed";
                }
            } else if ($messages) {
                $messages = [$messages];
            }
            if ($result['status_code'] === 401) {
                $errors[] = 'Authentication required';
            } else if ($result['status_code'] === 404) {
                $errors[] = 'Not found';
            } else if (!$messages) {
                $errors[] = $result['message'];
            } else {
                foreach ($messages as $message) {
                    $message = (isset($message['field']) ? $message['field'] . ': ' : '') . ($message['error_description'] ?? $message['error']);
                    $errors[] = $message;
                }
            }
            return implode(',', $errors);
        }
        return false;
    }

    private function getKey()
    {
        $mainKey = UnisendShippingConst::SETTING_KEY_LAST_ERROR;
        $userToken = $this->getToken();
        if ($userToken) {
            return $mainKey . '_' . strtolower($userToken);
        }
        return $mainKey;
    }

    private function getToken()
    {
        if (isset($_REQUEST['user_token'])) {
            return $_REQUEST['user_token'];
        }
        return $_REQUEST['token'] ?? null;
    }

}
