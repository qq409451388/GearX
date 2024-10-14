<?php
abstract class BuildAnnotation extends Anno
{
    /**
     * 指定注解的执行模式 @see AnnoPolicyEnum
     */
    public function constPolicy()
    {
        return AnnoPolicyEnum::POLICY_BUILD;
    }

    /**
     * 非必须，切面逻辑类名，触发此注解时，执行的逻辑
     * @return string the class ? extends Aspect|null
     * @example {@see DiAspect}
     */
    abstract public function constAspect();
}
