<?php

class Gear implements EzStarter
{
    /**
     * @var array<EzStarter>
     */
    private $registers = [];

    public function __construct(){
    }

    public function init() {
        $this->initObjects();
        array_unshift($this->registers, new AnnoationStarter());
        foreach ($this->registers as $register) {
            $register->init();
            $register->start();
        }
    }

    private function initObjects() {
        $classess = Application::getContext()->getGlobalComponentClass();
        if (!empty($classess)) {
            foreach ($classess as $class) {
                if (is_subclass_of($class, EzStarter::class)) {
                    $this->registers[] = new $class();
                }
            }
        }

        $classess = Application::getContext()->getAppSourceClass();
        if (!empty($classess)) {
            foreach($classess as $class) {
                $this->createBean($class);
            }
        }
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
            if(!$reflection->isSubclassOf(BaseController::class)) {
                continue;
            }
            $reflectionMethods = $reflection->getMethods();
            foreach($reflectionMethods as $reflectionMethod) {
                if(!$reflectionMethod->isPublic()
                    || BaseController::class === $reflectionMethod->getDeclaringClass()->getName()){
                    continue;
                }
                if (!$reflectionMethod->isUserDefined() || $reflectionMethod->isConstructor()) {
                    continue;
                }
                $defaultPath = $objName . '/' . $reflectionMethod->getName();
                EzRouter::get()->setMapping($defaultPath, $reflection->getName(), $reflectionMethod->getName());
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

    public function start()
    {
        // TODO: Implement start() method.
    }
}
