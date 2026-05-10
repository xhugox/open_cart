<?php

namespace unisend_shipping\services;


use unisend_shipping\cons\UnisendShippingConst;

/**
 * Singleton class
 */
class UnisendShippingService
{

    private static $instance = null;

    public function __construct()
    {
    }


    public static function install($db)
    {
        $db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unisend_shipping` (
				`unisend_id` INT NOT NULL AUTO_INCREMENT ,
				`order_id` INT NOT NULL ,
				`unisend_shipping_pickup_point` TEXT NOT NULL ,
				`status` VARCHAR(10) NOT NULL ,
				`tracking` VARCHAR(14) NOT NULL ,
				`manifest` VARCHAR(14) NOT NULL ,
				`packs` TEXT NOT NULL,
				`error_message` TEXT NOT NULL,
				PRIMARY KEY (`unisend_id`),
				UNIQUE (`order_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; 
		");

            $db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unisend_shipping_order` (
                  `order_id` bigint(20) NOT NULL,
                  `carrier_id` bigint(20) NOT NULL,
                  `barcode` varchar(255) DEFAULT NULL,
                  `shipping_code` varchar(255) NOT NULL,
                  `parcel_id` bigint(20) DEFAULT NULL,
                  `request_id` varchar(255) DEFAULT NULL,
                  `plan_code` varchar(20) DEFAULT NULL,
                  `parcel_type` varchar(20) DEFAULT NULL,
                  `weight` int(11) DEFAULT NULL,
                  `size` varchar(20) DEFAULT NULL,
                  `part_count` int(11) NOT NULL,
                  `pickup_address_id` bigint(20) DEFAULT NULL,
                  `status` varchar(255) NOT NULL,
                  `shipping_status` varchar(255) NULL,
                  `terminal_id` varchar(20) DEFAULT NULL,
                  `terminal` varchar(255) DEFAULT NULL,
                  `cod_amount` decimal(38,2) DEFAULT NULL,
                  `cod_selected` bit DEFAULT b'0',
                  `created` datetime(6) DEFAULT NULL,
                  `updated` datetime(6) DEFAULT NULL,
                  PRIMARY KEY (`order_id`),
                  KEY `IDX_unisend_shipping_order_created` (`created`),
                  KEY `IDX1eeooi5whyuq4n07dwdfdht85` (`status`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

        $db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unisend_shipping_method` (
            `unisend_shipping_method_id` INT NOT NULL AUTO_INCREMENT ,
            `title` TEXT NOT NULL ,
            `plan_code` TEXT NOT NULL ,
            `parcel_type` TEXT NOT NULL ,
            `rate_type` TEXT NOT NULL ,
            `free_shipping_from` DECIMAL( 10, 2 ),
            `is_deleted` BOOLEAN ,
            `sort_order` INT NULL,
            PRIMARY KEY (`unisend_shipping_method_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        $db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unisend_shipping_request` (
            `request_id` varchar(100) NOT NULL,
            `status` varchar(100) NOT NULL,
            `created` datetime NOT NULL,
            `updated` datetime NULL,
            PRIMARY KEY (`request_id`),
            INDEX (`status`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        $db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unisend_shipping_method_sizes` (
            `unisend_shipping_method_size_id` INT NOT NULL AUTO_INCREMENT ,
            `size` TEXT NOT NULL ,
            `price` DECIMAL( 10, 2 ) NOT NULL,
            `unisend_shipping_method_id` INT NOT NULL,
            FOREIGN KEY (unisend_shipping_method_id) REFERENCES " . DB_PREFIX . "unisend_shipping_method(unisend_shipping_method_id),
            PRIMARY KEY (`unisend_shipping_method_size_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        $db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unisend_shipping_method_weights` (
            `unisend_shipping_method_weight_id` INT NOT NULL AUTO_INCREMENT ,
            `weight_from` INT NOT NULL ,
            `weight_to` INT NOT NULL ,
            `price` DECIMAL( 10, 2 ) NOT NULL,
            `unisend_shipping_method_id` INT NOT NULL,
            FOREIGN KEY (unisend_shipping_method_id) REFERENCES " . DB_PREFIX . "unisend_shipping_method(unisend_shipping_method_id),
            PRIMARY KEY (`unisend_shipping_method_weight_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        $db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unisend_shipping_method_countries` (
            `unisend_shipping_method_country_id` INT NOT NULL AUTO_INCREMENT ,
            `code` TEXT NOT NULL ,
            `name` TEXT NOT NULL ,
            `unisend_shipping_method_id` INT NOT NULL,
            FOREIGN KEY (unisend_shipping_method_id) REFERENCES " . DB_PREFIX . "unisend_shipping_method(unisend_shipping_method_id),
            PRIMARY KEY (`unisend_shipping_method_country_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
    }

    public static function update($db)
    {
        $updatedVersion = UnisendShippingConfigService::get(UnisendShippingConst::SETTING_KEY_VERSION);
        if ($updatedVersion && $updatedVersion == UNISEND_SHIPPING_VERSION) {
            return false;
        }

        $result = $db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PREFIX . "unisend_shipping_method'")->rows;
        $columns = array();
        foreach ($result as $column) {
            $columns[] = $column['COLUMN_NAME'];
        }

        if (!in_array('sort_order', $columns)) {
            $db->query("ALTER TABLE `" . DB_PREFIX . "unisend_shipping_method` ADD sort_order INT NULL");
        }
        UnisendShippingConfigService::updateValue(UnisendShippingConst::SETTING_KEY_VERSION, UNISEND_SHIPPING_VERSION);
        UnisendShippingEshopService::onUpdate();
        return true;
    }

    public static function uninstall($db)
    {
        $db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unisend_shipping`");
        $db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unisend_shipping_method_sizes`");
        $db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unisend_shipping_method_weights`");
        $db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unisend_shipping_method_countries`");
        $db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unisend_shipping_method`");
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingService();
        }
        return self::$instance;
    }
}
