<?php

class DependencyCollector {

    /**
     * analyse module name&version from package.application.json
     * and return the sub dependencies until the package is empty
     * @param DependencyInfo $moduleInfo
     * @return array<DependencyInfo>
     */
    public static function analyse($moduleInfo) {
        if (!$moduleInfo instanceof DependencyInfo) {
            return [];
        }
        $moduleName = $moduleInfo->name;
        /**
         * catch the package application info from params of name & version
         */
        $modulePath = Application::getContext()->getGearPath().DIRECTORY_SEPARATOR."modules"
            .DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR."application.json";
        $json = file_get_contents($modulePath);
        if (empty($json)) {
            return [];
        }
        /**
         * The module info from moudule package.application.json
         * @var DependencyInfo $moduleInfo
         */
        $moduleInfoArr = EzCollectionUtils::decodeJson($json);
        $moduleInfo = EzObjectUtils::createObject($moduleInfoArr, DependencyInfo::class);
        return EzDataUtils::getFromObject($moduleInfo, "dependencies");
    }

    /**
     * todo 缓存已经加载的模块
     * @param DependencyInfo $dependency
     */
    public static function import($dependency) {
        $path = Application::getContext()->getGearPath().DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$dependency->name;
        if (!is_dir($path)) {
            echo "[WARN] The module $dependency->name is not exists!";
            return [];
        }
        $classes = SysUtils::scanFile($path, -1, ["php"], true);
        return $classes;
    }
}