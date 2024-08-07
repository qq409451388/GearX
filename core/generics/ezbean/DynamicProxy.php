<?php

/**
 * 动态代理类
 * @description 依赖 __call方法实现代理逻辑，本对象内的方法名以 “__call__” 开头确保业务方法名一定不存在
 */
class DynamicProxy
{
    /**
     * @var {to $object} $obj
     */
    private $obj;
    private $init;

    /**
     * @var EzReflectionClass
     */
    private $ref;
    /**
     * @var array<string, array<RunTimeFunction>>
     */
    private $callBefore;

    /**
     * @var array<string, array<RunTimeFunction>>
     */
    private $callAfter;

    private $contextInstance;

    public function __construct($object){
        $this->obj = $object;
        $this->init = false;
        $this->ref = new EzReflectionClass($this->obj);
        $this->callBefore = $this->callAfter = $this->contextInstance = [];
    }

    public function __CALL__isInit() {
        return $this->init;
    }

    public function __CALL__getReflectionClass() {
        return $this->ref;
    }

    public static function __CALL__get($object, $isInit = false):DynamicProxy{
        $dp = new static($object);
        $dp->init = true;
        return $dp;
    }

    public function __call($funcName, $args){
        try {
            if(method_exists($this, $funcName)){
                return call_user_func_array([$this, $funcName], $args);
            }
            $rpp = new RunTimeProcessPoint(get_class($this->obj), $funcName, $args, null);
            $rpp->setClassInstance($this->ref);
            $rpp->setContextInstanceList($this->contextInstance);
            if($this->__CALL__hasBefore($rpp->getFunctionName())){
                $this->__CALL__callBefore($rpp);
            }
            if(!$rpp->isSkip()){
                $return = call_user_func_array([$this->obj, $rpp->getFunctionName()], $rpp->getArgs());
                $rpp->setReturnValue($return);
            }
            if($this->__CALL__hasAfter($rpp->getFunctionName())){
                $this->__CALL__callAfter($rpp);
            }
            return $rpp->getReturnValue();
        } finally {
            $this->clearContextInstance();
        }

    }

    public function __CALL__getSourceObj(){
        return $this->obj;
    }

    private function __CALL__hasBefore($funcName){
        if (!empty($this->callBefore['*'])) {
            return true;
        }
        return array_key_exists($funcName, $this->callBefore);
    }

    private function __CALL__hasAfter($funcName){
        if (!empty($this->callAfter['*'])) {
            return true;
        }
        return array_key_exists($funcName, $this->callAfter);
    }

    public function __CALL__registeBefore($targetFunc, $anonyId, Closure $anony, $order = 0, $after = null){
        if (!isset($this->callBefore[$targetFunc])) {
            $this->callBefore[$targetFunc] = [];
        }
        $this->callBefore[$targetFunc][] = RunTimeFunction::create($targetFunc, $anonyId, $anony, $order, $after);
        $this->__CALL__reOrder($this->callBefore);
    }

    public function __CALL__registeAfter($targetFunc, $anonyId, Closure $anony, $order = 0, $after = null){
        if (!isset($this->callAfter[$targetFunc])) {
            $this->callAfter[$targetFunc] = [];
        }
        $this->callAfter[$targetFunc][] = RunTimeFunction::create($targetFunc, $anonyId, $anony, $order, $after);
        $this->__CALL__reOrder($this->callAfter);
    }

    public function __CALL__registeBeforeAll($anonyId, Closure $anony, $order){
        $this->__CALL__registeBefore("*", $anonyId, $anony, $order);
    }

    public function __CALL__registeAfterAll($anonyId, Closure $anony, $order){
        $this->__CALL__registeAfter("*", $anonyId, $anony, $order);
    }

    private function __CALL__callBefore(RunTimeProcessPoint $rpp){
        $calls = $this->callBefore['*'] ?? [];
        $calls += $this->callBefore[$rpp->getFunctionName()]??[];
        foreach($calls as $runTimeItem){
            $call = $runTimeItem->anonymous;
            $call($rpp);
        }
    }

    private function __CALL__callAfter(RunTimeProcessPoint $rpp){
        $calls = $this->callAfter['*'] ?? [];
        $calls += $this->callAfter[$rpp->getFunctionName()]??[];
        foreach($calls as $runTimeItem){
            $call = $runTimeItem->anonymous;
            $call($rpp);
        }
    }

    /**
     * @param array<string, array<RunTimeFunction>>$runtimeItemList
     * @return void
     */
    private function __CALL__reOrder(&$runtimeItemList) {
        foreach ($runtimeItemList as $funcName => &$runtimeItemListChildren) {
            $orderd = array_column($runtimeItemListChildren, "order", "anonymousId");
            asort($orderd);
            $runtimeItemListChildren = array_column($runtimeItemListChildren, null, "anonymousId");
            $runtimeItemListChildren = EzCollectionUtils::matchKeys(array_keys($orderd), $runtimeItemListChildren);
        }
    }

    public function addContextInstance($ins) {
        $this->contextInstance[] = $ins;
    }

    private function clearContextInstance() {
        $this->contextInstance = [];
    }
}
