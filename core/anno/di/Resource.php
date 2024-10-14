<?php

/**
 * 将对象注入到类属性中
 * 要求：待注入的类应为EzBean对象
 */
class Resource extends BuildAnnotation
{
    public function getClassName()
    {
        return $this->value;
    }

    public function constTarget()
    {
        return AnnoElementType::TYPE_FIELD;
    }

    public function constStruct()
    {
        return AnnoValueTypeEnum::TYPE_NORMAL;
    }

    public function constAspect()
    {
        return DiAspect::class;
    }
}
