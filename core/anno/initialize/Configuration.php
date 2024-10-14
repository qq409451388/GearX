<?php

/**
 * 初始化一个配置对象
 * 要求 注解传入一个配置前缀，配置的结构应和配置对象一致
 */
class Configuration extends BuildAnnotation
{

    /**
     * 指定注解可以放置的位置（默认: 所有）@see AnnoElementType
     */
    public function constTarget()
    {
        return [AnnoElementType::TYPE_CLASS];
    }

    /**
     * 指定注解的value设置规则 @see AnnoValueTypeEnum
     */
    public function constStruct()
    {
        return AnnoValueTypeEnum::TYPE_NORMAL;
    }

    /**
     * 非必须，切面逻辑类名，触发此注解时，执行的逻辑
     * @return string the class ? extends Aspect|null
     * @example {@see DiAspect}
     */
    public function constAspect()
    {
        return ConfigurationAspect::class;
    }
}
