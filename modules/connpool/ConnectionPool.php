<?php
class ConnectionPool implements EzComponent {
    /**
     * @var array<ConnectionPoolItemData>
     */
    private static $connectionPool;

    private static $connectionPoolInstances;

    private function __construct() {
        self::$connectionPoolInstances = [];
    }

    public static function setConfiguration($config) {
        self::$connectionPool = $config;
    }

    public static function getConfiguration() {
        return self::$connectionPool;
}

    public static function getInstance($className){
        $instances = self::$connectionPoolInstances[$className] ?? null;
        if (empty($instances)) {
            return null;
        }
        $randomIndex = self::getIndexFromPool($className);
        return $instances[$randomIndex];
    }

    private static function getIndexFromPool($className) {
        return mt_rand(0, count(self::$connectionPoolInstances[$className])-1);
    }

    public static function setConnection($connectionList){
        return self::$connectionPoolInstances = $connectionList;
    }
}
