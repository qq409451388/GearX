<?php

class EzBeanUtils
{
    /**
     * 创建Bean，$className类的代理类
     * @param string $className
     * @param boolean $isDeep 是否递归创建依赖的Bean 实验性功能默认关闭
     * @return DynamicProxy<$className>
     */
    public static function createBean(string $className, $isDeep = false) {
        if (interface_exists($className)) {
           return null;
        }
        DBC::assertTrue(class_exists($className), "[EzObject] ClassName $className is not found!",
            0, GearIllegalArgumentException::class);
        DBC::assertTrue(is_subclass_of($className, EzBean::class), "[EzObject] Class Must implements EzBean, But {$className}!",
            0, GearIllegalArgumentException::class);
        $refClass = new EzReflectionClass($className);
        if ($refClass->isAbstract() || $refClass->isInterface()) {
            return null;
        }
        $class = BeanFinder::get()->pull($className);
        if ($class instanceof DynamicProxy && $class->__CALL__isInit()) {
            return $class;
        }
        $class = new $className;
        if ($isDeep) {
            $properties = $refClass->getProperties();
            foreach ($properties as $property) {
                $propertyDoc = $property->getDocComment();
                if (empty($propertyDoc)) {
                    continue;
                }
                $annoItem = $property->getAnnoation(Clazz::get(Resource::class));
                $property->forceSetValue($class, self::createBean($annoItem->value, $isDeep));
            }
        }
        $dp = DynamicProxy::__CALL__get($class, $isDeep);
        if ($isDeep) {
            BeanFinder::get()->save($className, $dp);
        }
        return $dp;
    }
}
