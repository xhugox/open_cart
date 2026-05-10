<?php

namespace unisend_shipping\services;


use DVDoug\BoxPacker\Box;
use DVDoug\BoxPacker\Item;
use DVDoug\BoxPacker\ItemList;
use DVDoug\BoxPacker\VolumePacker;
use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\context\UnisendShippingContextHolder;

/**
 * Singleton class
 */
class UnisendShippingSizeService
{
    private static $instance = null;

    public function __construct() {}

    public static function resolveSize($orderData)
    {
        $products = $orderData['products'] ?? null;
        if (empty($products)) return null;
        $packages = [];
        $defaultWidthCfg = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_WIDTH);
        $defaultHeightCfg = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_HEIGHT);
        $defaultLengthCfg = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_DEFAULT_DIMENSION_LENGTH);
        $defaultWidth = $defaultWidthCfg && is_numeric($defaultWidthCfg) ? (float)$defaultWidthCfg : 10;
        $defaultHeight = $defaultHeightCfg && is_numeric($defaultHeightCfg) ? (float)$defaultHeightCfg : 10;
        $defaultLength = $defaultLengthCfg && is_numeric($defaultLengthCfg) ? (float)$defaultLengthCfg : 10;

        $lengthClass = UnisendShippingContextHolder::getInstance()->getLength();
        $unisendLengthClassId = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_DEFAULT_LENGTH_CLASS_ID) ?: 1;
        foreach ($products as $product) {
            $virtual = isset($product['shipping']) && $product['shipping'] != true;
            $length = (float)$lengthClass->convert($product['length'], $product['length_class_id'], $unisendLengthClassId);
            $width = (float)$lengthClass->convert($product['width'], $product['length_class_id'], $unisendLengthClassId);
            $height = (float)$lengthClass->convert($product['height'], $product['length_class_id'], $unisendLengthClassId);
            if (!$virtual) {
                $packages[] = [$width > 0 ? $width : $defaultWidth, $height > 0 ? $height : $defaultHeight, $length > 0 ? $length : $defaultLength, $product['quantity'] ?? 1];
            }
        }
        $packages = self::packageSize($packages, $orderData);
        return !empty($packages) ? strtoupper($packages[0]) : UnisendShippingConst::DEFAULT_BOX_SIZE;
    }

    private static function getAvailableSizes($orderData)
    {
        if (!isset($orderData['unisendCarrier'])) {
            return [];
        }
        $planCode = $orderData['unisendCarrier']['plan_code'];
        if ($planCode === 'HANDS' || $planCode === 'TERMINAL') {
            return self::getBpSizes($orderData);
        }
        return self::getLpSizes($orderData);
    }

    private static function getLpSizes($orderData)
    {
        $weight = self::getWeight($orderData);
        if ($weight <= 500) {
            $sizes['s'] = [2, 38.1, 30.5];
        }
        if ($weight <= 2000) {
            $sizes['m'] = [60, 60, 60];
        }
        $sizes['l'] = [105, 105, 105];
        return $sizes;
    }

    private static function getWeight($orderData)
    {
        if (isset($orderData['orderInfo']['weight'])) {
            return max($orderData['orderInfo']['weight'], 1);
        }
        return 1;
    }

    private static function getBpSizes($orderData)
    {
        return [
            'xs' => [18.5, 61, 8],
            's' => [35, 61, 8],
            'm' => [35, 61, 17.5],
            'l' => [35, 61, 36.5],
            'xl' => [35, 61, 74.5],
        ];
    }

    private static function packageSize($packages, $orderData)
    {
        $sizes = self::getAvailableSizes($orderData);

        $boxes = [];
        foreach ($sizes as $key => $value) {
            $boxes[] = new SimpleBox($key, $value[0] * 10, $value[1] * 10, $value[2] * 10, 0, $value[0] * 10, $value[1] * 10, $value[2] * 10, 0);
        }

        $items = new ItemList();
        foreach ($packages as $package) {
            $items->insert(new SimpleItem('', $package[0] * 10, $package[1] * 10, $package[2] * 10, 0, 1, true));
        }

        $possible_sizes = [];
        foreach ($boxes as $box) {
            $packer = new VolumePacker($box, $items);
            $packedBox = $packer->pack();
            if ($packedBox->getItems()->count()  === $items->count()) {
                $possible_sizes[] = $box->getReference();
            }
        }

        return $possible_sizes;
    }


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingSizeService();
        }
        return self::$instance;
    }
}

class SimpleBox implements Box
{
    private string $reference;
    private int $width;
    private int $length;
    private int $depth;

    public function __construct(string $reference, int $width, int $length, int $depth) {
        $this->reference = $reference;
        $this->width = $width;
        $this->length = $length;
        $this->depth = $depth;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getOuterWidth(): int
    {
        return $this->width;
    }

    public function getOuterLength(): int
    {
        return $this->length;
    }

    public function getOuterDepth(): int
    {
        return $this->depth;
    }

    public function getEmptyWeight(): int
    {
        return 0;
    }

    public function getInnerWidth(): int
    {
        return $this->width;
    }

    public function getInnerLength(): int
    {
        return $this->length;
    }

    public function getInnerDepth(): int
    {
        return $this->depth;
    }

    public function getMaxWeight(): int
    {
        return PHP_INT_MAX; // disables weight
    }
}

class SimpleItem implements Item
{
    private string $description;
    private int $width;
    private int $length;
    private int $depth;
    private int $quantity;
 
    public function __construct(string $description, int $width, int $length, int $depth, int $quantity = 1)
    {
        $this->description = $description;
        $this->width = $width;
        $this->length = $length;
        $this->depth = $depth;
        $this->quantity = $quantity;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getWeight(): int
    {
        return 0;
    }

    public function getAllowedRotation(): bool
    {
        return true;
    }

    public function getKeepFlat(): bool
    {
        return false;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
