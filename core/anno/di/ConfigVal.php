<?php

class ConfigVal extends Anno
{

    /**
     * 指定注解可以放置的位置（默认: 所有）@see AnnoElementType
     */
    public static function constTarget()
    {
        return AnnoElementType::TYPE_FIELD;
    }

    /**
     * 指定注解的执行模式 @see AnnoPolicyEnum
     */
    public static function constPolicy()
    {
        return AnnoPolicyEnum::POLICY_BUILD;
    }

    /**
     * 指定注解的value设置规则 @see AnnoValueTypeEnum
     */
    public static function constStruct()
    {
        return AnnoValueTypeEnum::TYPE_NORMAL;
    }

    /**
     * 非必须，切面逻辑类名，触发此注解时，执行的逻辑
     * @example {@see DiAspect}
     */
    public static function constAspect()
    {
        return DiAspect::class;
    }
}
