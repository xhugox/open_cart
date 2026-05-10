<?php

namespace unisend_shipping\api;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendDocumentApi extends UnisendApi
{

    const STICKER_PDF_URI = 'documents/cn/pdf';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }

    public static function downloadCnPdfs(array $orderIds): bool
    {
        $instance = self::getInstance();
        $stickersResponse = $instance->get(self::STICKER_PDF_URI, $instance->getParams($orderIds), 'application/pdf' . ',' . self::DEFAULT_ACCEPT . ';q=0.9');
        if ($stickersResponse) {
            $filename = sprintf('lp_cn_%s.pdf',
                date('Y-m-d H:i:s')
            );
            header('Content-type: application/pdf');
            header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
            echo $stickersResponse;
            return true;
        }
        return false;
    }

    private static function getParams(array $orderIds): array
    {
        $params["idRefs"] = implode(',', $orderIds);
        return $params;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendDocumentApi();
        }
        return self::$instance;
    }
}
