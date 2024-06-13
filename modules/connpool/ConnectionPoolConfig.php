<?php
class ConnectionPoolConfig implements EzDataObject {
    public $module;
    public $driver;
    public $aliveSeconds;
    /**
     * @var array<ConnectionPoolInstance>
     */
    public $instances = [];
}
