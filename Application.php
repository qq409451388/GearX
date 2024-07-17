<?php

/**
 * 初始化依赖，只调用基础函数
 * @description 使用方式：定义项目路径常量PROJECT_PATH, 在启动文件require once该文件，调用启动方法即可
 */
class Application
{
    /**
     * @var array<EzStarter>
     */
    private $starterRegisters = [];

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
        if (is_array($context)) {
            $context = new ApplicationContext($context);
        } else if (!$context instanceof ApplicationContext) {
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

    /**
     * Rewrite file path to support both Windows format (C:\xxx\xxx) and Unix format (/xxx/xxx or ~/xxx).
     * When a folder name is passed in, treat the project path as the root path.
     * @return void
     */
    private function rewritePath() {
        // rewrite path if $v is only a folder name
        foreach (Application::$context->getPaths() as $k => $v) {
            if (empty($v)) {
                continue;
            }

            if (!is_dir($v)) {
                if (!self::isPlainFolderName($v)) {
                    exit("The $k <$v> is not a folder name!");
                }
                // treat the project path as the root path.
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
    }

    private static function isPlainFolderName($folderName) {
        // 根据不同操作系统设置不同的正则表达式
        if (self::isWin()) {
            // Windows 文件夹名称不能包含 \/:*?"<>| 以及控制字符
            $pattern = '/^[^\/\\\\:*?"<>|\x00-\x1F]+$/';
        } else {
            // Unix-like (Linux, macOS) 文件夹名称不能包含 / 以及控制字符
            $pattern = '/^[^\/\x00-\x1F]+$/';
        }

        // 使用 preg_match 检查是否匹配
        return preg_match($pattern, $folderName) === 1;
    }

    /**
     * Support Unix-style paths starting with “~”
     * @param string $path
     * @return string
     */
    private static function rewritePathForUnix($path) {
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
        $classes = array_keys($hash);
        Application::$context->setGlobalCoreClass($classes);
    }

    private function outputVersion() {
        $bannerPath = Application::$context->getCorePath().DIRECTORY_SEPARATOR."generics".DIRECTORY_SEPARATOR."banner";
        $files = SysUtils::scanFile($bannerPath);
        $bannerFile = $files[mt_rand(0, count($files) - 1)];

        $versionInfo = file_get_contents( Application::$context->getGearPath().DIRECTORY_SEPARATOR."gearx.version");
        $versionInfo = trim($versionInfo);
        echo PHP_EOL;
        print_r(file_get_contents($bannerFile));
        usleep(200000);
        echo PHP_EOL;
        Logger::info("[:::GearX:::] version:{}", $versionInfo);
        usleep(500000);
    }

    protected function loadAppContainer() {
        $hash = $this->getFilePaths(Application::$context->getAppSourceClassPath());
        $this->register($hash);
        $classes = array_keys($hash);
        Application::$context->setAppSourceClass($classes);

        foreach($hash as $className => $classPath) {
            if (is_subclass_of($className, EzBean::class)) {
                $this->createBean($className);
            }
        }
    }

    private function startRegister() {
        Logger::info("[Application] Start Register...");
        array_unshift($this->starterRegisters, new AnnoationStarter());
        foreach ($this->starterRegisters as $register) {
            Logger::info("[Application] Init Third StartRegister {}", get_class($register));
            $register->init();
            $register->start();
            Logger::info("[Application] Inited {} StartRegister Success!", get_class($register));
        }
    }

    protected function loadModulePackagesAndRegister() {
        $dependencies = Config::get("application.dependency");
        if (is_null($dependencies)) {
            return;
        }
        Logger::info("[Application] Start Searching modules...");
        $hash = self::searchModules(EzObjectUtils::createObjectList($dependencies, DependencyInfo::class));
        $this->register($hash);
        $classes = [];
        // regist Components
        foreach ($hash as $className => $classPath) {
            if (is_subclass_of($className, EzComponent::class)) {
                $classes[] = $className;
            }

            if (is_subclass_of($className, EzStarter::class)) {
                $this->starterRegisters[] = Clazz::get($className)->new();
            }

            if (is_subclass_of($className, EzBean::class)) {
                $this->createBean($className);
            }
        }
        Application::$context->setGlobalComponentClass($classes);
    }

    /**
     * @param array<DependencyInfo> $dependencies
     * @return array
     */
    private static function searchModules($dependencies, $parentModule = null) {
        $classes = [];
        $preFix = empty($parentModule) ? "" : $parentModule.'->';
        foreach ($dependencies as $dependency) {
            Logger::info("[Application] >>>>>> Import module: [$preFix$dependency->name] <<<<<<");
            $classes += DependencyCollector::import($dependency);
            Logger::info("[Application] Analyse sub module: $preFix$dependency->name");
            $subDependencies = DependencyCollector::analyseSubDependencies($dependency);
            if (!empty($subDependencies)) {
                Logger::info("[Application] Subdependencies found, Extra Searching subdependencies...");
                $classes += self::searchModules($subDependencies, $preFix.$dependency->name);
            } else {
                Logger::info("[Application] No subdependencies found.");
            }
        }
        return $classes;
    }

    private function getFilePaths($path) {
        if (!is_dir($path)) {
            exit("[error] The path $path is not exists!");
        }
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

    private function register($hash) {
        spl_autoload_register(function ($className) use($hash){
            $filePath = $hash[$className] ?? "";
            if(file_exists($filePath)){
                include($filePath);
            }
        });
    }

    /**
     * 自动注册继承自BaseController的公共函数的路由
     * 路径规则：类名/方法名
     * @deprecated
     * @return void
     * @throws ReflectionException
     */
    protected function initRouter() {
        /**
         * @var DynamicProxy $obj
         */
        foreach(BeanFinder::get()->getAll(DynamicProxy::class) as $objName => $obj) {
            $reflection = $obj->__CALL__getReflectionClass();
            if(!$reflection->isSubclassOf("BaseController")) {
                continue;
            }
            $reflectionMethods = $reflection->getMethods();
            foreach($reflectionMethods as $reflectionMethod) {
                if(!$reflectionMethod->isPublic()
                    || "BaseController" === $reflectionMethod->getDeclaringClass()->getName()){
                    continue;
                }
                if (!$reflectionMethod->isUserDefined() || $reflectionMethod->isConstructor()) {
                    continue;
                }
                $defaultPath = $objName . '/' . $reflectionMethod->getName();
                BeanFinder::get()->fetch(EzRouter::class)
                    ->setMapping($defaultPath, $reflection->getName(), $reflectionMethod->getName());
            }
        }
    }

    /**
     * create an obj if none in objects[]
     * @param string $class
     * @return void
     * @throws Exception
     */
    private function createBean($class){
        if (!is_subclass_of($class, EzBean::class)) {
            return;
        }
        try {
            /**
             * isDeep传false， 交由注解逻辑:startAnno()统一注入
             */
            $bean = EzBeanUtils::createBean($class, false);
            if (is_null($bean)) {
                return;
            }
            BeanFinder::get()->save($class, $bean);
            Logger::console("[Gear]Create Bean {$class}");
        } catch (Exception $e) {
            DBC::throwEx("[Gear]Create Bean Exception {$e->getMessage()}", 0, GearShutDownException::class);
        }
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
    public static function loadScript($applicationArguments = null) {
        $app = new self($applicationArguments);
        $app->envConfiguration();
        $app->loadCore();
        // for Logger
        Env::setRunModeScript();
        Config::init();
        $app->outputVersion();
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
    public static function loadWebServer($applicationArguments = null) {
        $app = new self($applicationArguments);
        $app->envConfiguration();
        $app->loadCore();
        Env::setRunModeConsole();
        Config::init();
        $app->outputVersion();
        $app->loadModulePackagesAndRegister();
        $app->loadAppContainer();
        $app->startRegister();
        return $app;
    }
}

/**
 * collect php args
 * such as :
 * 1: normal -Pa=1
 * 2: array  -PAb=1,2,3
 * 3: object  -POc[a]=1 -POc[b]=2
 */
class ApplicationContext {
    private $appPath;
    private $appSourceClassPath;
    private $gearPath;
    private $corePath;
    /**
     * Gear-defined dependency-related configuration
     * default at [gearPath]/config
     */
    private $defaultConfigPath;
    /**
     * User-defined dependency-related configuration
     * for instead of the <defaultConfigPath>
     */
    private $systemConfigPath;
    /**
     * User-defined parameter attribute configuration
     * default at [appPath]/config is exists
     */
    private $configPath;
    private $globalCoreClass = [];
    private $globalComponentClass = [];
    private $appSourceClass = [];
    private $configuration = null;

    public function __construct($args = []) {
        foreach ($args as $arg) {
            // 匹配 -PA<Key>=[<Value1>,<Value2>,...] 列表格式
            if (preg_match('/^-PA(\w+)=\[(.*?)\]$/', $arg, $matches)) {
                $k = $matches[1];
                $v = $matches[2] !== '' ? explode(',', $matches[2]) : [];
                if (empty($k) || empty($v)) {
                    echo "[".date("Y-m-d H:i:s")."][WARN]Invalid argument $arg and ignored.".PHP_EOL;
                    continue;
                }
                if (!property_exists($this, $k)) {
                    echo "[".date("Y-m-d H:i:s")."][WARN]Unknown property $k for argument {$matches[2]} and ignored.".PHP_EOL;
                    continue;
                }
                $this->$k = $v;
            } else if (preg_match('/^-PO(\w+)\[(\w+)\]=(.*)$/', $arg, $matches)) {
                // 匹配 -PO<Key>[<SubKey>]=<Value> 嵌套数组格式
                $k = $matches[1];
                $k2 = $matches[2];
                $v = $matches[3];
                if (empty($k) || empty($k2) || empty($v)) {
                    echo "[".date("Y-m-d H:i:s")."][WARN]Invalid argument $arg and ignored.".PHP_EOL;
                    continue;
                }
                if (!property_exists($this, $k)) {
                    echo "[".date("Y-m-d H:i:s")."][WARN]Unknown property $k for argument $v and ignored.".PHP_EOL;
                    continue;
                }
                if ($this->$k === null) {
                    $this->$k = [];
                }
                $this->$k[$k2] = $v;
            }else if (preg_match('/^-P(\w+)=(.*)/', $arg, $matches)) {
                // 匹配 -P<Key>=<Value> 格式
                $k = $matches[1];
                $v = $matches[2];
                if (empty($k) || empty($v)) {
                    echo "[".date("Y-m-d H:i:s")."][WARN]Invalid argument $arg and ignored.".PHP_EOL;
                    continue;
                }
                if (!property_exists($this, $k)) {
                    echo "[".date("Y-m-d H:i:s")."][WARN]Unknown property $k for argument $v and ignored.".PHP_EOL;
                    continue;
                }
                $this->$k = $v;
            }
        }
    }

    public function build() {
        if (empty($this->appPath)) {
            $this->appPath = dirname(__FILE__, 2);
            if (is_dir($this->appPath) && is_dir($this->appPath."/src")) {
                echo "[".date("Y-m-d H:i:s")."][WARN]App path not specified, loading default [project_path] configuration".PHP_EOL;
            }
        }
        $this->appSourceClassPath = $this->appPath."/src";
        if (!is_dir($this->appPath) || !is_dir($this->appSourceClassPath)) {
            exit("[error] The arguments <appPath> must be set at valid project Root Path!");
        }
        if (empty($this->gearPath)) {
            echo "[".date("Y-m-d H:i:s")."][WARN]Gear framework path not specified, loading default [project_path/GearX] configuration".PHP_EOL;
            $this->setGearPath($this->appPath."/GearX");
        } else {
            $this->setGearPath($this->gearPath);
        }
    }

    public function getPaths() {
        return [
            "configPath" => $this->configPath,
            "gearPath" => $this->gearPath,
            "corePath" => $this->corePath
        ];
    }

    public function getAppPath() {
        return $this->appPath;
    }

    public function setAppPath($appPath): void {
        $this->appPath = $appPath;
    }

    public function getGearPath() {
        return $this->gearPath;
    }

    public function setGearPath($gearPath): void {
        $this->gearPath = $gearPath;
        $this->corePath = $gearPath."/core";
        $this->defaultConfigPath = $gearPath."/config";
    }

    public function getCorePath() {
        return $this->corePath;
    }

    public function getDefaultConfigPath() {
        return $this->defaultConfigPath;
    }

    public function getSystemConfigPath() {
        return $this->systemConfigPath;
    }

    public function setSystemConfigPath($systemConfigPath): void {
        $this->systemConfigPath = $systemConfigPath;
    }

    public function getConfigPath() {
        return $this->configPath;
    }

    public function setConfigPath($configPath): void {
        $this->configPath = $configPath;
    }

    public function getGlobalCoreClass(): array {
        return $this->globalCoreClass;
    }

    public function setGlobalCoreClass(array $globalCoreClass): void {
        $this->globalCoreClass = $globalCoreClass;
    }

    public function getGlobalComponentClass(): array {
        return $this->globalComponentClass;
    }

    public function setGlobalComponentClass(array $globalClass): void {
        $this->globalComponentClass = $globalClass;
    }

    public function getAppSourceClassPath() {
        return $this->appSourceClassPath;
    }

    public function setAppSourceClassPath($appSourceClassPath): void {
        $this->appSourceClassPath = $appSourceClassPath;
    }

    public function getAppSourceClass() {
        return $this->appSourceClass;
    }

    public function setAppSourceClass($appSourceClass): void {
        $this->appSourceClass = $appSourceClass;
    }
}
