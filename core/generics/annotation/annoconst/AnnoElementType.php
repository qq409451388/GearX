<?php

class AnnoElementType
{
    public const TYPE = 1;
    public const TYPE_FIELD = 2;
    public const TYPE_METHOD = 3;
    public const PARAMETER = 4;
    public const TYPE_CLASS = 5;

    private static $descMap = [
        self::TYPE_METHOD => "Method",
        self::TYPE_CLASS => "Class",
        self::TYPE_FIELD => "Property",
        self::TYPE => "Any Where"
    ];

    public static function getDesc($expected)
    {
        if (EzCheckUtils::isList($expected)) {
            return implode(",", array_map(function ($item) {
                return self::getDesc($item);
            }, $expected));
        }
        return self::$descMap[$expected];
    }
}
