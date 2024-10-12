<?php

/**
 * 将对象注入到类属性中
 * 要求：待注入的类应为EzBean对象
 */
class Resource extends Anno
{
    public function getClassName()
    {
        return $this->value;
    }

    public static function constTarget()
    {
        return AnnoElementType::TYPE_FIELD;
    }

    public static function constPolicy()
    {
        return AnnoPolicyEnum::POLICY_BUILD;
    }

    public static function constStruct()
    {
        return AnnoValueTypeEnum::TYPE_NORMAL;
    }

    public static function constAspect()
    {
        return DiAspect::class;
    }
}
