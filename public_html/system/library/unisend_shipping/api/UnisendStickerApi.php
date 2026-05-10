<?php

namespace unisend_shipping\api;

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\services\UnisendShippingConfigService;

require_once(dirname(__FILE__) . '/UnisendApi.php');

/**
 * Singleton class to make calls to API
 */
class UnisendStickerApi extends UnisendApi
{

    const ORIENTATION_PORTRAIT = "PORTRAIT";
    const ORIENTATION_LANDSCAPE = "LANDSCAPE";
    const LAYOUT_MAX = "LAYOUT_MAX";
    const LAYOUT_A4 = "LAYOUT_A4";
    const LAYOUT_10X15 = "LAYOUT_10x15";

    const STICKER_LIST_URI = 'sticker/list';
    const STICKER_PDF_URI = 'sticker/pdf';

    private static $instance = null;

    public function __construct()
    {
        parent::__construct();
    }


    public static function getStickers(array $orderIds, bool $includeCn, bool $includeManifest)
    {
        $instance = self::getInstance();
        return $instance->get(self::STICKER_LIST_URI, $instance->getParams($orderIds, $includeCn, $includeManifest));
    }

    public static function downloadStickersPdf(array $orderIds, bool $includeCn = true, bool $includeManifest = false): bool
    {
        $instance = self::getInstance();
        $stickersResponse = $instance->get(self::STICKER_PDF_URI, $instance->getParams($orderIds, $includeCn, $includeManifest), 'application/pdf' . ',' . self::DEFAULT_ACCEPT . ';q=0.9');
        if ($stickersResponse) {
            $filename = sprintf('lp_labels_%s.pdf',
                date('Y-m-d H:i:s')
            );
            header('Content-type: application/pdf');
            header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
            echo $stickersResponse;
            return true;
        }
        return false;
    }

    private static function getParams(array $orderIds, bool $includeCn, bool $includeManifest): array
    {
        $params["idRefs"] = implode(',', $orderIds);

        $layout = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_STICKER_LAYOUT);
        if (!$layout) {
            $layout = self::LAYOUT_10X15;
        }
        $params["layout"] = $layout;
        $orientation = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_STICKER_ORIENTATION);
        if ($orientation) {
            $params["labelOrientation"] = $orientation;
        }
        if ($includeCn) {
            $params["includeCn23"] = $includeCn;
        }
        if ($includeManifest) {
            $params["includeManifest"] = $includeManifest;
        }
        return $params;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendStickerApi();
        }
        return self::$instance;
    }
}
