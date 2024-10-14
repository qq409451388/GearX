<?php

class Autowired extends BuildAnnotation
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
