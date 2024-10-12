<?php
class EzWeakObjectContainer extends EzObject implements EzDataObject
{
    private $obj;
    private $createTime;
    private $aliveTimeSeconds;
    /**
     * @var Closure $refreshLogic
     */
    private $refreshLogic;

    public function equals($target) {
        if (is_null($target)) {
            return false;
        }
        return EzObjectUtils::equals($this->obj, $target);
    }

    public function __construct($obj, $aliveTimeSeconds = 6, $refreshLogic = null) {
        $this->createTime = time();
        $this->obj = $obj;
        $this->aliveTimeSeconds = $aliveTimeSeconds;
        $this->refreshLogic = $refreshLogic;
    }

    public function isAlive():bool {
        return ($this->createTime + $this->aliveTimeSeconds) > (time()+1);
    }

    public function __call($name, $arguments){
        $this->refresh();
        DBC::assertTrue($this->isAlive(), "[".get_class($this->obj)."] the instance is not alive.");
        return $this->obj->$name(...$arguments);
    }

    public function __get($name) {
        $this->refresh();
        DBC::assertTrue($this->isAlive(), "[".get_class($this->obj)."] the instance is not alive.");
        return $this->obj->$name;
    }

    public function __set($name, $value) {
        $this->refresh();
        DBC::assertTrue($this->isAlive(), "[".get_class($this->obj)."] the instance is not alive.");
        $this->obj->$name = $value;
    }

    public function refresh() {
        if ($this->isAlive() || !is_callable($this->refreshLogic)) {
            return;
        }
        $class = get_class($this->obj);
        Logger::info("Refresh Resource For Instance of $class.");
        $newObj = call_user_func_array($this->refreshLogic, array($this->obj));
        $id = spl_object_id($this->obj);
        if (spl_object_id($newObj) !== $id) {
            $this->createTime = time();
            $this->obj = $newObj;
        } else {
            Logger::error("[$class] refresh failed. the object spl id is $id");
        }
    }
}
