<?php
class ConnectionPoolItemData implements EzDataObject {
    public $module;
    public $driver;
    /**
     * @var array<ConnectionPoolInstance>
     */
    public $instances = [];
}
