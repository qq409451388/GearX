<?php

/**
 * 依赖注入切面
 */
class DiAspect extends Aspect implements BuildAspect
{

    public function check(): bool
    {
        return true;
    }

    public function adhere(): void
    {
        if (Resource::class == $this->getAnnoName()) {
            $className = $this->getValue()->className;
            $object = BeanFinder::get()->pull($className);
            DBC::assertTrue(
                $object instanceof DynamicProxy,
                "[DiAspect] 待注入对象{$className}必须为DynamicProxy实例!",
                0,
                GearShutDownException::class
            );
            $this->getAtProperty()->setAccessible(true);
            $classObj = BeanFinder::get()->pull($this->getAtClass()->getName());
            DBC::assertTrue(
                $classObj instanceof DynamicProxy,
                "[DiAspect] 被注入对象{$this->getAtClass()->getName()}必须为DynamicProxy实例!",
                0,
                GearShutDownException::class
            );
            $this->getAtProperty()->setValue($classObj->__CALL__getSourceObj(), $object);
            $this->getAtProperty()->setAccessible(false);
        }
        if (Autowired::class == $this->getAnnoName()) {
            // todo
            return;
            BeanFinder::get()->analyseClasses();
            $className = $this->getValue()->className;

            if (!BeanFinder::get()->has($className)) {
                BeanFinder::get()->save($className, (new $className));
            }
            $object = BeanFinder::get()->pull($className);
            $this->getAtProperty()->setAccessible(true);
            $classObj = BeanFinder::get()->pull($this->getAtClass()->getName());
            $classObj = $classObj instanceof DynamicProxy ? $classObj->__CALL__getSourceObj() : $classObj;
            $this->getAtProperty()->setValue($classObj, $object);
            $this->getAtProperty()->setAccessible(false);
        }
        if (ConfigVal::class == $this->getAnnoName()) {
            $classObj = BeanFinder::get()->pull($this->getAtClass()->getName());
            $configValue = $this->getValue()->value;
            $this->getAtProperty()->setAccessible(true);
            $this->getAtProperty()->setValue($classObj->__CALL__getSourceObj(), Config::get($configValue));
            $this->getAtProperty()->setAccessible(false);
        }
    }
}
