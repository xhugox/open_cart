<?php

namespace unisend_shipping\context;


/**
 * Singleton class
 */
class UnisendShippingContextHolder
{

    private $db;
    private $datasource;
    private $loader;
    private $weight;
    private $length;
    private $cache;

    private static $instance = null;

    public function __construct()
    {
    }


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new UnisendShippingContextHolder();
        }
        return self::$instance;
    }

    public static function load($module)
    {
        $instance = self::getInstance();
        $instance->db = $module->db;
        $instance->loader = $module->load;
        $instance->loader->model('setting/setting');
        $instance->datasource = $module->model_setting_setting;
        $instance->weight = $module->weight;
        $instance->length = $module->length;
        $instance->cache = $module->cache;
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return mixed
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * @return mixed
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return mixed
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getCache()
    {
        return $this->cache;
    }


}
