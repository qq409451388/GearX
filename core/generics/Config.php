<?php

/**
 * dependency: EzCollectionUtils, Logger, Application, DBC, SysUtils, GearShutDownException
 */
class Config
{
    /**
     * @var array $config
     */
    private static $config = [];
    private const KEY_SPLIT = ".";
    private const EXT_JSON = "json";

    public static function init() {
        // always at first
        self::initSystemConfig();
        self::initStaticConfig();
        Logger::info("[Configuration] active env:{}", self::get("application.env"));
    }


    /**
     * Necessary configurations for project operation, such as dependency package configurations,
     * variable configurations required for project runs, etc.
     *
     * Before configuring, please clarify your key value names to avoid conflicts with system reserved key values due to exceptions.
     *
     *
     * If certain configuration files are not desired to be tracked by the project version control,
     * you can use this feature to set as needed.
     * It is recommended to store some secret keys, account information,
     * and other private data externally, rather than on the VCS.
     * @return void
     */
    private static function initStaticConfig() {
        if (!empty(Application::getContext()->getConfigPath())) {
            $configPath = Application::getContext()->getConfigPath();
        } else {
            $configPath = Application::getContext()->getAppPath().DIRECTORY_SEPARATOR."config";
            if (!is_dir($configPath)) {
                return;
            }
            Logger::warn("Gear framework's Config path not specified, loading default[project_path/config] configuration.");
        }
        self::initConfigFromFile($configPath);
    }

    /**
     * For some necessary configurations that the Gear framework relies on for operation.
     *
     * Custom modifications are allowed, but you must understand the consequences of doing so.
     * @return void
     */
    private static function initSystemConfig() {
        $systemConfigPath = empty(Application::getContext()->getSystemConfigPath()) ?
            Application::getContext()->getDefaultConfigPath() :
            Application::getContext()->getSystemConfigPath();
        self::initConfigFromFile($systemConfigPath);
    }

    private static function initConfigFromFile($configPath) {
        DBC::assertTrue(is_dir($configPath),
            "[Config] The specified path for CONFIG_PATH <$configPath> does not exist. Please ensure that the path is correct and try again.",
            0, GearShutDownException::class);
        $pjs = SysUtils::scanFile($configPath, -1, [self::EXT_JSON], true);
        $applicationConfig = [];
        foreach ($pjs as $key => $pj) {
            if (!is_file($pj)) {
                return null;
            }
            if ((new EzString($key))->startsWith("application")) {
                $applicationConfig[$key] = $pj;
                continue;
            }
            $content = file_get_contents($pj);
            if(!empty($content) && $decodedObj = EzCodecUtils::decodeJson($content)){
                self::setFromFile($key, $decodedObj);
            }
        }

        if (!empty($applicationConfig)) {
            // refresh application
            $env = null;
            $applicationConfigObject = [];
            if (isset($applicationConfig['application'])) {
                $json = file_get_contents($applicationConfig['application']);
                $applicationConfigObject = EzCodecUtils::decodeJson($json);
                $env = $applicationConfigObject['env'];
            }
            if (defined("ENV")) {
                $env = ENV;
            }
            if (!empty(Application::getContext()->getEnv())) {
                $env = Application::getContext()->getEnv();
            }
            DBC::assertNotEmpty($env, "[Config] The Env must be specified at Config File application.");
            $applicationInstanceKey = "application.$env";
            if (isset($applicationConfig[$applicationInstanceKey])) {
                $json = file_get_contents($applicationConfig[$applicationInstanceKey]);
                $tmp = EzCodecUtils::decodeJson($json);
                foreach ($tmp as $key => $value) {
                    $applicationConfigObject[$key] = $value;
                }
            } else {
                Logger::warn("[Config] The env was set to {}, but no application.{} configuration was found, using the default configuration instead.", $env, $env);
            }
            self::setFromFile('application', $applicationConfigObject);
        }

    }

    private static function setFromFile($key, $data) {
        self::$config[$key] = $data;
    }

    public static function get($key, $default = null) {
        if(empty($key)){
            return is_null($default) ? null : $default;
        }
        $keyArr = explode(self::KEY_SPLIT, $key);
        $tmpRes = self::$config;
        foreach ($keyArr as $index => $k) {
            if (!isset($tmpRes[$k])) {
                return is_null($default) ? null : $default;
            }
            $tmpRes = $tmpRes[$k];
        }
        return is_null($tmpRes) ? $default : $tmpRes;
    }

    public static function getRecursion($p = ""){
        return empty($p) ? self::$config : self::get($p);
    }

    public static function set($arr){
        foreach($arr as $k => $v){
            self::setOne($k, $v);
        }
    }

    public static function setOne($k, $v) {
        $arr = explode(self::KEY_SPLIT, $k);
        $tmp = &self::$config;
        $maxIndex = count($arr) - 1;
        foreach ($arr as $index => $key) {
            if ($index == $maxIndex) {
                $tmp[$key] = $v;
            } else {
                if (!isset($tmp[$key])) {
                    $tmp[$key] = []; // 确保创建不存在的中间数组
                }
                $tmp = &$tmp[$key];
            }
        }
    }

    public static function add($key, $item) {
        $list = self::get($key);
        if (is_null($list)) {
            $list = [];
        }
        if (is_array($list)) {
            $list[] = $item;
            self::setOne($key, $list);
        }
    }

    public static function getAll() {
        return self::$config;
    }
}
