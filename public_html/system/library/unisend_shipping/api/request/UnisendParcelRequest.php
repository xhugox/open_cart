<?php

namespace unisend_shipping\api\request;

use unisend_shipping\cons\UnisendShippingConst;
use unisend_shipping\services\UnisendShippingConfigService;
use unisend_shipping\services\UnisendShippingOrderService;
use unisend_shipping\services\UnisendShippingSizeService;

class UnisendParcel
{
    public $type;
    public $size = null;
    public $weight;
    public $partCount = 1;
}

class UnisendPlan
{
    public $code;
}

class UnisendService
{
    public $code;
    public $value = null;
}

class UnisendPerson
{
    public $name;
    public $companyName = null;
    public $contacts;
    public $address;
}

class UnisendContacts
{
    public $phone = null;
    public $email = null;
    public $fax = null;
}

class UnisendAddress
{
    public $municipality;
    public $locality;
    public $flat;
    public $building;
    public $address;
    public $address1;
    public $address2;
    public $terminalId;
    public $district;
    public $street;
    public $postalCode;
    public $countryCode;
    public $subDistrict;

    public function setMunicipality($municipality): void
    {
        $this->municipality = $municipality;
    }

    public function setLocality($locality): void
    {
        $this->locality = $locality;
    }

    public function setFlat($flat): void
    {
        $this->flat = $flat;
    }

    public function setBuilding($building): void
    {
        $this->building = $building;
    }

    public function setAddress($address): void
    {
        $this->address = $address;
    }

    public function setAddress1($address1): void
    {
        $this->address1 = $address1;
    }

    public function setAddress2($address2): void
    {
        $this->address2 = $address2;
    }

    public function setTerminalId($terminalId): void
    {
        $this->terminalId = $terminalId;
    }

    public function setDistrict($district): void
    {
        $this->district = $district;
    }

    public function setStreet($street): void
    {
        $this->street = $street;
    }

    public function setPostalCode($postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function setCountryCode($countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function setSubDistrict($subDistrict): void
    {
        $this->subDistrict = $subDistrict;
    }

}

class UnisendDocuments
{
    public $cn;
}

class UnisendCn
{
    const CN_PARCEL_TYPE = 'SELL';
    const CN_PARCEL_TYPE_NOTES = 'Sell Items';
    public $contentType;


    public $contentDescription;
    public $failureInstruction;
    public $importer;
    public $exporter;
    public $parts;

    public static function from($orderData): UnisendCn
    {
        $cnForm = $orderData['cnForm'];
        $cn = new UnisendCn();
        $cn->contentType = $cnForm['contentType'];
        $cn->contentDescription = $cnForm['contentDescription'];
        $cn->parts = $cnForm['parts'];
        return $cn;
    }

    public static function fromOrderData($order): UnisendCn
    {
        $orderProducts = $order['orderProducts'];
        $products = $order['products'];
        $cn = new UnisendCn();
        $cnParts = [];

        foreach ($orderProducts as $orderProduct) {
            $product = $products[array_search($orderProduct['product_id'], array_column($products, 'product_id'))];
            $virtual = isset($product['shipping']) && $product['shipping'] != true;
            if ($virtual) continue;
            $weightInGrams = UnisendShippingOrderService::getInstance()->getProductWeight($product, isset($product['total']) ? 1 : $orderProduct['quantity']);
            $quantity = intval($orderProduct['quantity']);
            $summary = $product['name'] != null ? substr($product['name'], 0, 64) : null;
            $cnParts[] = [
                'summary' => $summary ? mb_convert_encoding($summary, "UTF-8", "UTF-8") : null,
                'amount' => $product['price'],
                'currencyCode' => 'EUR',
                'weight' => max($weightInGrams, 1),
                'quantity' => $quantity
            ];
        }
        $cn->contentType = self::CN_PARCEL_TYPE;
        $cn->contentDescription = 'Sell Items';//TODO translate
        $cn->parts = $cnParts;
        return $cn;
    }

}

class UnisendCnImporter
{
    public $taxCode;
    public $vatCode;
    public $code;
    public $customsRegistrationNo;
    public $contact;
    public $documents;
}

class UnisendCnExporter
{
    public $customsRegistrationNo;
}

class UnisendCnDocuments
{
    public $license;
    public $certificate;
    public $invoice;
    public $notes;
}

class UnisendCnPart
{
    public $summary;
    public $weight;
    public $quantity;
    public $hsCode;
    public $amount;
    public $currencyCode;
    public $countryCode;
}

class UnisendParcelRequest
{
    public $senderAddressId = null;
    public $pickupAddressId = null;
    public $comment = 'opencart';
    public $overwriteIdRef = false;
    public $idRef;
    public $parcel;
    public $plan;
    public $receiver;
    public $pickup;
    public $documents;
    public $services;
    public $source;

    public static function fromOrderData($orderData): UnisendParcelRequest
    {
        $orderInfo = $orderData['orderInfo'];
        $unisendCarrier = $orderData['unisendCarrier'] ?? [];

        $parcel = new UnisendParcel();
        $parcel->weight = max($orderInfo['weight'] ?? UnisendShippingOrderService::getInstance()->getOrderWeight($orderData), 1);
        $parcel->type = $orderInfo['parcelType'] ?? $unisendCarrier['parcel_type'];
        $orderData['orderInfo']['weight'] = $parcel->weight;
        $parcel->size = $orderInfo['size'] ?? UnisendShippingSizeService::resolveSize($orderData);
        $parcel->partCount = $orderInfo['partCount'] ?? 1;

        $plan = new UnisendPlan();
        $plan->code = $orderInfo['planCode'] ?? $unisendCarrier['plan_code'];

        $receiver = self::personFromOrderInfo($orderInfo);
        if ($parcel->type === 'H2T' || $parcel->type === 'T2T' || $parcel->type === 'T2S') {
            $receiver->address->terminalId = $orderData['unisend_selected_terminal_id'] ?? null;
        }

        $request = new UnisendParcelRequest();
        $request->idRef = $orderInfo['order_id'] ?? null;

        $request->parcel = $parcel;
        $request->plan = $plan;
        $request->receiver = $receiver;
        $cnRequest = $_POST['cnForm'] ?? UnisendCn::fromOrderData($orderData);
        $request->documents = new UnisendDocuments();
        $request->documents->cn = $cnRequest;
        if (UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_PICKUP_ENABLED) == true) {
            $pickupAddressId = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_ADDRESS_PICKUP_ID);
            $request->pickupAddressId = $pickupAddressId;
        }
        $services = [];
        if ((isset($orderInfo['codSelected']) && $orderInfo['codSelected'] == 1 && isset($orderInfo['codAmount'])) ||
            (isset($_POST['codSelected']) && $_POST['codSelected'] == 1 && isset($_POST['codAmount']))) {
            $service = new UnisendService();
            $service->code = 'COD';
            $service->value = $orderInfo['codAmount'] ?? $_POST['codAmount'];
            $services[] = $service;
        }
        $request->services = $services;
        return $request;
    }

    public static function personFromOrderInfo($orderInfo): UnisendPerson
    {
        return self::toReceiver($orderInfo);
    }

    public static function toReceiver($orderInfo): UnisendPerson
    {

        $phone = $orderInfo['telephone'];

        $receiver = new UnisendPerson();
        $receiver->address = new UnisendAddress();
        $receiver->contacts = new UnisendContacts();

        $receiver->companyName = self::nullIfEmpty($orderInfo['shipping_company'] ?? null);
        $receiver->name = self::nullIfEmpty(trim($orderInfo['shipping_firstname'] . ' ' . $orderInfo['shipping_lastname'])) . self::toReceiverIdRef($orderInfo);
        $receiver->address = self::toReceiverAddress($orderInfo);
        $receiver->contacts->phone = self::nullIfEmpty($phone);
        $receiver->contacts->email = self::nullIfEmpty($orderInfo['email']);

        return $receiver;
    }

    public static function toReceiverAddress($orderInfo): UnisendAddress
    {
        $address = new UnisendAddress();
        $address->setCountryCode(self::nullIfEmpty($orderInfo['shipping_iso_code_2']));
        $address->setLocality(self::nullIfEmpty($orderInfo['shipping_city']));
        $address->setPostalCode(self::nullIfEmpty($orderInfo['shipping_postcode']));
        $address->setAddress(self::nullIfEmpty($orderInfo['shipping_address_1'] . ' ' . $orderInfo['shipping_address_2'] ?? null));
        
        return $address;
    }

    private static function nullIfEmpty($value)
    {
        if ($value == "") return null;
        return $value;
    }

    private static function toReceiverIdRef($orderInfo)
    {
        if (!isset($orderInfo['order_id'])) {
            return null;
        }
        return ' [#' . $orderInfo['order_id'] . ']';
    }
}
