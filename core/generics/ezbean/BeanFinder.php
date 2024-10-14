<?php
class BeanFinder
{
    private static $ins;
    private $objects = [];

    /**
     * @var EzTree $tree
     */
    private $tree;

    public static function get(){
        if(null == self::$ins){
            self::$ins = new self();
        }
        return self::$ins;
    }

    public function has($key){
        return array_key_exists(strtolower($key), $this->objects);
    }

    public function replace($key, $obj){
        DBC::assertTrue($this->has(strtolower($key)), "[BeanFinder] $key is not exists");
        $this->objects[$key] = $obj;
    }

    public function save($key, $obj){
        if (is_null($obj)) {
            Logger::warn("[BeanFinder] save $key is null");
            return;
        }
        $key = strtolower($key);
        if($this->has($key)){
            Logger::warn("[BeanFinder] $key is exists!");
        }
        $this->objects[$key] = $obj;
    }

    public function pull($key){
        if(is_null($key)){
            DBC::throwEx("[BeanFinder] pull failed,key is null");
        }
        $key = strtolower($key);
        return $this->objects[$key] ?? null;
    }

    public function fetch($key) {
        if ($this->has($key)) {
            return $this->pull($key);
        }
        if (class_exists($key)) {
            $this->import($key);
            return $this->pull($key);
        }
        return null;
    }

    public function getAll($filter = null){
        if (is_null($filter)) {
            return $this->objects;
        }
        return array_filter($this->objects, function($object) use ($filter) {
            return get_class($object) == $filter;
        });
    }

    public function import($className){
        //$clazz =  Clazz::get($className);
        $this->save(strtolower($className), EzBeanUtils::createBean($className, false));
        Logger::info("[Bean] Create DynamicObject {$className}");
    }

    public function importLite($className) {
        $ref = new EzReflectionClass($className);
        if ($ref->isInterface() || $ref->isAbstract()) {
            return;
        }
        $this->save(strtolower($className), new $className());
        Logger::info("[Bean] Create NormalObject {$className}");
    }
}
