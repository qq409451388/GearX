<?php

class DependencyCollector {

    /**
     * @param DependencyInfo $moduleInfo
     * @return array<DependencyInfo>
     */
    public static function analyse($moduleInfo) {
        $moduleName = $moduleInfo->name;
        $modulePath = Application::getContext()->getGearPath().DIRECTORY_SEPARATOR."modules"
            .DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR."application.json";
        $json = file_get_contents($modulePath);
        if (empty($json)) {
            return [];
        }
        /**
         * @var DependencyInfo $moduleInfo
         */
        $moduleInfo = EzBeanUtils::createObject(EzCollectionUtils::decodeJson($json), DependencyInfo::class);
        $subdependencies = $moduleInfo->dependencies;
        return $subdependencies;
    }

    /**
     * todo 缓存已经加载的模块
     * @param DependencyInfo $dependency
     * @return void
     */
    public static function import($dependency) {
        $path = Application::getContext()->getGearPath().DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$dependency->name;
        if (!is_dir($path)) {
            echo "[WARN] The module $d is not exists!";
            return [];
        }
        $classes = SysUtils::scanFile($path, -1, ["php"], true);
        return $classes;
    }
}