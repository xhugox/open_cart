<?php

namespace unisend_shipping\api\request;


class UnisendIdRefListRequest
{
    public $idRefs;

    public static function from($orderIds): UnisendIdRefListRequest
    {
        $request = new UnisendIdRefListRequest();
        $request->idRefs = $orderIds;
        return $request;
    }
}
