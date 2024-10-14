<?php

/**
 * 将配置数据注入到类属性中
 * 要求：待注入的类应为EzBean对象
 */
class ConfigVal extends BuildAnnotation
{
    /**
     * 指定注解可以放置的位置（默认: 所有）@see AnnoElementType
     */
    public function constTarget()
    {
        return AnnoElementType::TYPE_FIELD;
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
     * @example {@see DiAspect}
     */
    public function constAspect()
    {
        return DiAspect::class;
    }
}
