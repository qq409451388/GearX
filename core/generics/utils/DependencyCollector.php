<?php

class DependencyCollector {
    private static $cacheName = [];
    private static $cache = [];

    /**
     * analyse module name&version from package.application.json
     * and return the sub dependencies until the package is empty
     * @param DependencyInfo $moduleInfo
     * @return array<DependencyInfo>
     */
    public static function analyseSubDependencies($moduleInfo) {
        if (!$moduleInfo instanceof DependencyInfo) {
            return [];
        }
        $moduleName = $moduleInfo->name;
        if (in_array($moduleName, self::$cacheName)) {
            Logger::info("[Dependency] The module $moduleName has been loaded!");
            return [];
        }
        self::$cacheName[] = $moduleName;
        /**
         * catch the package application info from params of name & version
         */
        $modulePath = Application::getContext()->getGearPath().DIRECTORY_SEPARATOR."modules"
            .DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR."application.json";
        if (!file_exists($modulePath)) {
            return [];
        }
        $json = file_get_contents($modulePath);
        if (empty($json)) {
            return [];
        }
        /**
         * The module info from moudule package.application.json
         * @var DependencyInfo $moduleInfo
         */
        $moduleInfoArr = EzCodecUtils::decodeJson($json);
        $moduleInfo = EzObjectUtils::create($moduleInfoArr, DependencyInfo::class);
        DBC::assertTrue($moduleInfo->phpVersionCheck(), "[Dependency] php version check fail! module:".$moduleInfo->name
            , 999, GearShutDownException::class);
        DBC::assertEmpty(SysUtils::extensionLoaded($moduleInfo->phpExtensions), "[Dependency] php extension check fail! module:".$moduleInfo->name);
        return EzObjectUtils::getFromObject($moduleInfo, "dependencies");
    }

    /**
     * todo 缓存已经加载的模块
     * @param DependencyInfo $dependency
     */
    public static function import($dependency) {
        if (in_array($dependency->name, self::$cache)) {
            return [];
        }
        self::$cache[] = $dependency->name;
        $path = Application::getContext()->getGearPath().DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$dependency->name;
        if (!is_dir($path)) {
            Logger::warn("[Dependency] The module $dependency->name is not exists!");
            return [];
        }
        $classes = SysUtils::scanFile($path, -1, ["php"], true);
        return $classes;
    }

    public static function hasModuel($moduleName):bool {
        return in_array($moduleName, self::$cache) || in_array($moduleName, self::$cacheName);
    }
}
