<?php

/**
 * 初始化依赖，只调用基础函数
 * @description 使用方式：定义项目路径常量PROJECT_PATH, 在启动文件require once该文件，调用启动方法即可
 */
class Application
{
    /**
     * @var ApplicationContext $context
     */
    protected static $context;

    const OS_LINUX = "UNIX";
    const OS_WINDOWS = "WINDOWS";
    const OS_MAC = "MACOS";

    /**
     * @param ApplicationContext|null $context
     */
    public function __construct($context) {
        if (!$context instanceof ApplicationContext) {
            $context = new ApplicationContext();
        }
        Application::$context = $context;
    }

    public static function getContext() {
        return Application::$context;
    }

    private function envConfiguration() {
        // The App Path is nessary
        Application::$context->build();
        $this->pathCheck("App Path", Application::$context->getAppPath());
        $this->rewritePath();
    }

    public function rewritePath() {
        // rewrite path if $v is only a folder name
        foreach (Application::$context->getPaths() as $k => $v) {
            if (empty($v)) {
                continue;
            }
            if (self::isWin()) {
                // rewrite path for windows
                if (false === strpos($v, "/")) {
                    $v = Application::$context->getAppPath() . DIRECTORY_SEPARATOR . $v;
                }
            } else {
                if (false === strpos($v, DIRECTORY_SEPARATOR)) {
                    $v = Application::$context->getAppPath() . DIRECTORY_SEPARATOR . $v;
                }
            }
            $v = self::rewritePathForUnix($v);
            if (!is_dir($v)) {
                exit("The $k <$v> is not exists!");
            }
            $func = "set" . ucfirst($k);
            Application::$context->$func($v);
        }
    }

    /**
     * Support Unix-style paths starting with “~”
     * @param string $path
     * @return string
     */
    public static function rewritePathForUnix($path) {
        if (self::isUnix() && 0 === strpos($path, "~/")) {
            $home = self::getHome();
            return str_replace("~/", $home."/", $path);
        }
        return $path;
    }
    
    private function pathCheck($pathName, $path) {
        if (empty($path)) {
            exit("[error] The $pathName is empty!");
        }
        if (!is_dir($path)) {
            exit("[error] The path $pathName is not exists!");
        }
    }

    protected function loadCore() {
        $hash = $this->getFilePaths(Application::$context->getCorePath());
        $this->register($hash);
        Application::$context->setGlobalCoreClass(array_keys($hash));
    }

    // todo 类加载 区分场景，http、tcp等
    protected function loadWebServerContainer() {
        if (!defined("USER_PATH")) {
            $this->setPath("USER_PATH", PROJECT_PATH."/src");
        }
        $hash = $this->getFilePaths(USER_PATH);
        $this->register($hash);
        Config::set(["GLOBAL_USER_CLASS"=>array_keys($hash)]);
    }

    protected function initConfig() {
        Config::init();
    }

    protected function loadModulePackages() {
        $dependencies = Config::get("dependency");
        if (is_null($dependencies)) {
            return;
        }
        $this->loadModules($dependencies);
    }

    public function loadModules(array $modules) {
        $packages = Config::get("package");
        $dependencies = EzCollectionUtils::matchKeys($modules, $packages);
        $hash = SysUtils::searchModules($dependencies);
        $this->register($hash);
        foreach ($hash as $className => $classPath) {
            if (is_subclass_of($className, EzComponent::class)) {
                Config::add("GLOBAL_COMPONENTS", $className);
            }
        }
    }

    private function getFilePaths($path)
    {
        $hash = [];
        //过滤掉点和点点
        $map = array_filter(scandir($path), function($var) {
            return $var[0] != '.';
        });
        foreach ($map as $item) {
            $curPath = $path.'/'.$item;
            if(is_dir($curPath)){
                if($item == '.' || $item == '..'){
                    continue;
                }
                $hash += $this->getFilePaths($curPath);
            }
            if(false === strpos($item,".php")){
                continue;
            }
            if(is_file($curPath)){
                $className = str_replace('.php','',$item);
                //$curPath = str_replace("/", "\\", $curPath);
                $hash[$className] = $curPath;
            }
        }
        return $hash;
    }

    /**
     * 获取系统家族名称
     * @return string
     */
    public static function getSimlpeOs() {
        if (defined("PHP_OS_FAMILY")) {
            switch (PHP_OS_FAMILY) {
                case "Windows":
                    return self::OS_WINDOWS;
                case "BSD":
                case "Linux":
                case "Solaris":
                    return self::OS_LINUX;
                case "Darwin":
                    return self::OS_MAC;
                case "Unknown":
                default:
                    return "";

            }
        } else if (defined("PHP_OS")) {
            switch (PHP_OS) {
                case "Linux":
                    return self::OS_LINUX;
                case "Darwin":
                    return self::OS_MAC;
                case "WINNT":
                case "WIN32":
                case "Windows":
                    return self::OS_WINDOWS;
                default:
                    return "";
            }
        } else {
            return "";
        }
    }

    protected static function getHome() {
        if (self::isWin()) {
            return getenv("HOMEDRIVE").getenv("HOMEPATH");
        } else {
            return getenv("HOME");
        }
    }

    public static function isWin() {
        return self::OS_WINDOWS === self::getSimlpeOs();
    }

    public static function isLinux() {
        return self::OS_LINUX === self::getSimlpeOs();
    }

    public static function isUnix() {
        return self::isLinux() || self::isMac();
    }

    public static function isMac() {
        return self::OS_MAC === self::getSimlpeOs();
    }

    protected function register($hash) {
        spl_autoload_register(function ($className) use($hash){
            $filePath = $hash[$className] ?? "";
            if(file_exists($filePath)){
                include($filePath);
            }
        });
    }

    /**
     * The Script Mode Startup
     * 1.Environment Variable Configuration
     * 2.Core Loading
     * 3.Configuration Injection
     * 4.Dependency Package Loading
     * @param $applicationArguments
     * @return self
     */
    public static function runScript(ApplicationContext $applicationArguments = null) {
        $app = new self($applicationArguments);
        $app->envConfiguration();
        $app->loadCore();
        Env::setRunModeScript();
        $app->initConfig();
        $app->loadModulePackages();
        return $app;
    }

    /**
     * The WebServer Mode Startup
     * 1.Environment Variable Configuration
     * 2.Core Loading
     * 3.Configuration Injection
     * 4.Dependency Package Loading
     * 5.User Bean Injection & Web Container Loading
     * @param $constants
     * @return self
     */
    public static function runWebServer(ApplicationContext $applicationArguments = null) {
        if (is_null($applicationArguments)) {
            $applicationArguments = new ApplicationContext();
        }
        $app = new self();
        $app->envConstants($applicationArguments->getConstants());
        $app->loadCore();
        Env::setRunModeConsole();
        $app->initConfig();
        $app->loadModulePackages();
        $app->loadWebServerContainer();
        return $app;
    }

    /**
     * The Schedule Task Mode Startup
     * 1.Environment Variable Configuration
     * 2.Core Loading
     * 3.Configuration Injection
     * 4.Dependency Package Loading
     * @param $constants
     * @return SchduleTaskApplication
     */
    public static function runSchduleTask($constants = null) {
        $app = new self();
        $app->envConstants($constants);
        $app->loadCore();
        Env::setRunModeConsole();
        $app->initConfig();
        $app->loadModulePackages();
        $app->loadSchduleTaskModule();
        return new SchduleTaskApplication($app);
    }
}

class ApplicationContext {
    private $appPath;
    private $defaultConfigPath;
    private $configPath;
    private $gearPath;
    private $corePath;
    private $globalCoreClass = [];

    public function build() {
        if (empty($this->appPath)) {
            echo "[".date("Y-m-d H:i:s")."][WARN]App path not specified, loading default [project_path] configuration".PHP_EOL;
            $this->appPath = dirname(__FILE__, 2);
        }
        if (empty($this->gearPath)) {
            echo "[".date("Y-m-d H:i:s")."][WARN]Gear framework path not specified, loading default [project_path/GearX] configuration".PHP_EOL;
            $this->gearPath = $this->appPath."/GearX";
        }
        $this->defaultConfigPath = $this->gearPath."/config";
    }

    public function getGearPath(): string
    {
        return $this->gearPath;
    }

    /**
     * @return mixed
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * @return mixed
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    public function getPaths() {
        return [
            "configPath" => $this->configPath,
            "gearPath" => $this->gearPath,
            "corePath" => $this->gearPath."/core",
        ];
    }

    public function setAppPath(string $appPath): void
    {
        $this->appPath = $appPath;
    }

    /**
     * @param mixed $configPath
     */
    public function setConfigPath($configPath): void
    {
        $this->configPath = $configPath;
    }

    public function setGearPath(string $gearPath): void
    {
        $this->gearPath = $gearPath;
    }

    /**
     * @return mixed
     */
    public function getCorePath()
    {
        return $this->corePath;
    }

    /**
     * @param mixed $corePath
     */
    public function setCorePath($corePath): void
    {
        $this->corePath = $corePath;
    }

    public function getGlobalCoreClass(): array
    {
        return $this->globalCoreClass;
    }

    public function setGlobalCoreClass(array $globalCoreClass): void
    {
        $this->globalCoreClass = $globalCoreClass;
    }

    /**
     * @return mixed
     */
    public function getDefaultConfigPath()
    {
        return $this->defaultConfigPath;
    }

}