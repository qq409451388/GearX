<?php

class ConnectionPoolStarter implements EzStarter
{

    public function init()
    {
        $connectionPool = EzObjectUtils::createObjectList(Config::get("connectionPool"), ConnectionPoolItemData::class);
        foreach ($connectionPool as $key => $connectionPoolItemData) {
            if (!DependencyCollector::hasModuel($connectionPoolItemData->module)) {
                Logger::error("[ConnectionPool] Cannot Find module:{}, please check the configuration connectionPool!", $connectionPoolItemData->module);
                unset($connectionPool[$key]);
                continue;
            }
            if (empty($connectionPoolItemData->instances)) {
                Logger::error("[ConnectionPool] Cannot Find instance, when load module {}, please check the configuration connectionPool!", $connectionPoolItemData->module);
                unset($connectionPool[$key]);
                continue;
            }
        }
        ConnectionPool::setConfiguration($connectionPool);
    }

    public function start()
    {
        $connectionPoolInstanceObjects = [];
        $connectionPool = ConnectionPool::getConfiguration();
        foreach ($connectionPool as $index => $connectionPoolItemData) {
            $connectionPoolInstances = $connectionPoolItemData->instances;
            foreach ($connectionPoolInstances as $instanceIndex => $connectionPoolInstance) {
                $connectionPoolInstanceConfig = $connectionPoolInstance->config;
                try {
                    $connection = Clazz::get($connectionPoolItemData->driver)->callStatic(
                        $connectionPoolInstanceConfig->getStartMethod(), $connectionPoolInstanceConfig->getArgs());
                    if ($connectionPoolInstance->hook instanceof ConnectionPoolInstanceConfig) {
                        $method = $connectionPoolInstance->hook->startMethod;
                        call_user_func_array([$connection, $method], $connectionPoolInstance->hook->args);
                    }
                    $connectionPoolInstanceObjects[$connectionPoolItemData->driver][$instanceIndex] = $connection;
                } catch (Exception|Error $e) {
                    unset($connectionPoolItemData->instances[$instanceIndex]);
                    unset($connectionPoolInstanceObjects[$connectionPoolItemData->driver][$instanceIndex]);
                    Logger::error("[ConnectionPool Exception] Create Connection {}[{}] Failed! Caused by:{}",
                        $connectionPoolItemData->module, $instanceIndex, $e->getMessage());
                }
            }
            if (empty($connectionPoolItemData->instances)) {
                unset($connectionPool[$index]);
                unset($connectionPoolInstanceObjects[$connectionPoolItemData->driver]);
                Logger::error("[ConnectionPool] The module:{} Has No Instances!", $connectionPoolItemData->module);
            }
        }
        ConnectionPool::setConfiguration($connectionPool);
        ConnectionPool::setConnection($connectionPoolInstanceObjects);
    }
}
