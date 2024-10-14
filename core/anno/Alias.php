<?php

/**
 * used for @see EzObjectUtils::createNormalObject()
 */
class Alias extends RuntimeAnnotation
{
    public function getColumn() {
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

}
