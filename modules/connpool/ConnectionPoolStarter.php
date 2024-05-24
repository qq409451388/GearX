<?php

class ConnectionPoolStarter implements EzStarter
{
    /**
     * @var array<ConnectionPoolItemData>
     */
    private $connectionPool;

    public function init()
    {
        $this->connectionPool = EzObjectUtils::createObjectList(Config::get("connectionPool"), ConnectionPoolItemData::class);
        foreach ($this->connectionPool as $key => $connectionPoolItemData) {
            if (!DependencyCollector::hasModuel($connectionPoolItemData->module)) {
                Logger::error("[ConnectionPool] Cannot Find module:{}, please check the configuration connectionPool!", $connectionPoolItemData->module);
                unset($this->connectionPool[$key]);
                continue;
            }
            if (empty($connectionPoolItemData->instances)) {
                Logger::error("[ConnectionPool] Cannot Find instance, when load module {}, please check the configuration connectionPool!", $connectionPoolItemData->module);
                unset($this->connectionPool[$key]);
                continue;
            }
        }
    }

    public function start()
    {
        foreach ($this->connectionPool as $index => $connectionPoolItemData) {
            $connectionPoolInstances = $connectionPoolItemData->instances;
            foreach ($connectionPoolInstances as $instanceIndex => $connectionPoolInstance) {
                $connectionPoolInstanceConfig = $connectionPoolInstance->config;
                try {
                    $connectionPoolInstance->connection
                        = Clazz::get($connectionPoolItemData->driver)->callStatic(
                        $connectionPoolInstanceConfig->getStartMethod(), $connectionPoolInstanceConfig->getArgs());
                    if ($connectionPoolInstance->hook instanceof ConnectionPoolInstanceConfig) {
                        $method = $connectionPoolInstance->hook->startMethod;
                        call_user_func_array([$connectionPoolInstance->connection, $method], $connectionPoolInstance->hook->args);
                    }
                } catch (Exception|Error $e) {
                    unset($connectionPoolItemData->instances[$instanceIndex]);
                    Logger::error("[ConnectionPool Exception] Create Connection {}[{}] Failed! Caused by:{}",
                        $connectionPoolItemData->module, $instanceIndex, $e->getMessage());
                }
            }
            if (empty($connectionPoolItemData->instances)) {
                unset($this->connectionPool[$index]);
                Logger::error("[ConnectionPool] The module:{} Has No Instances!", $connectionPoolItemData->module);
            }
        }
    }
}
