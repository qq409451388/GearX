<?php

class EzReflectionClass extends ReflectionClass
{
    use EzReflectionTrait;

    /**
     * 获取类上的所有注解
     * @return array<AnnoationElement>
     */
    public function getAnnoationList() {
        return AnnoationRule::searchAnnoationFromDocument($this->getDocComment(), AnnoElementType::TYPE_CLASS);
    }

    /**
     * 获取属性中的指定注解
     * @param Clazz $annoClazz
     * @return array<AnnoationElement>
     * @throws ReflectionException
     */
    public function getPropertyAnnotationList(Clazz $annoClazz) {
        $properties = $this->getProperties();
        $annoList = [];
        foreach ($properties as $property) {
            $annoItem = $property->getAnnoation($annoClazz);
            if ($annoItem instanceof AnnoationElement) {
                $annoList[$property->getName()] = $annoItem;
            }
        }
        return $annoList;
    }

    /**
     * @param $filter
     * @return EzReflectionMethod[]
     * @throws ReflectionException
     */
    public function getMethods($filter = null):array {
        $methods = parent::getMethods($filter);
        $list = [];
        foreach ($methods as $method) {
            $list[] = new EzReflectionMethod($this->getName(), $method->getName());
        }
        return $list;
    }

    /**
     * @param $name
     * @return EzReflectionMethod
     * @throws ReflectionException
     */
    public function getMethod($name) : EzReflectionMethod {
        return new EzReflectionMethod($this->getName(), $name);
    }

    /**
     * @param $filter
     * @return EzReflectionProperty[]
     * @throws ReflectionException
     */
    public function getProperties($filter = null):array {
        $properties = parent::getProperties($filter);
        $list = [];
        foreach ($properties as $property) {
            $list[] = new EzReflectionProperty($this->getName(), $property->getName());
        }
        return $list;
    }

    /**
     * @param $name
     * @return EzReflectionProperty
     * @throws ReflectionException
     */
    public function getProperty($name):EzReflectionProperty {
        return new EzReflectionProperty($this->getName(), $name);
    }
}
