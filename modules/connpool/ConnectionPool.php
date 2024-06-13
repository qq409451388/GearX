<?php
class ConnectionPool implements EzComponent {
    /**
     * @var array<ConnectionPoolConfig>
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
        DBC::assertNotEmpty($instances, "[ConnectionPool] No Instances of $className", -1, GearNotFoundResourceException::class);
        $randomIndex = self::getIndexFromPool($className);
        return $instances[$randomIndex];
    }

    private static function getIndexFromPool($className) {
        return mt_rand(0, count(self::$connectionPoolInstances[$className])-1);
    }

    public static function setConnection($connectionList){
        return self::$connectionPoolInstances = $connectionList;
    }

    public static function getConnections($className){
        return self::$connectionPoolInstances[$className] ?? [];
    }
}
