<?php
class EzCheckUtils
{
    private static $dataTypeMap = [
        "int" => "Integer",
        "integer" => "Integer",
        "float" => "Double",
        "double" => "Double",
        "string" => "String",
        "array" => "Array",
        "resource" => "Resource"
    ];

    private static $scalarTypeList = [
        "Integer",
        "Double",
        "String"
    ];

    public static function isEmail($email) {
        return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function argsCheck(...$args)
    {
        foreach ($args as $arg) {
            if ((!is_numeric($arg) && empty($arg)) || (is_numeric($arg) && 0 > $arg)) {
                return false;
            }
        }
        return true;
    }

    public static function isJson($obj)
    {
        if (!is_string($obj)) {
            return false;
        }
        return self::isArray(EzCodecUtils::decodeJson($obj));
    }

    public static function isArray($obj)
    {
        return is_array($obj);
    }

    public static function isString($obj) {
        return is_string($obj);
    }

    public static function isList($array)
    {
        if (!self::isArray($array)) {
            return false;
        }
        $i = 0;
        foreach ($array as $k => $v) {
            if ($k !== $i++) {
                return false;
            }
        }
        return true;
    }

    public static function isMap($array) {
        return is_array($array) && !self::isList($array);
    }

    /**
     * 是否是一个Map
     * @param $array
     * @return bool
     */
    public static function isMapAdvance($array, $keyTypeExpect, $valueTypeExpect)
    {
        $isNotList = is_array($array) && !self::isList($array);
        if (!$isNotList) {
            return false;
        }
        foreach ($array as $k => $v) {
            $keyType = gettype($k);
            DBC::assertTrue(
                EzCheckUtils::dataTypeNameEquals($keyType, $keyTypeExpect),
                "[EzObject] Match data Fail! The Map Key " . EzObjectUtils::toString(
                    $k
                ) . " Must Be Type Of $keyTypeExpect, But " . $keyType,
                0,
                GearIllegalArgumentException::class
            );
            $valueType = gettype($v);
            DBC::assertTrue(
                EzCheckUtils::dataTypeNameEquals($valueType, $valueTypeExpect),
                "[EzObject] Match data Fail! The Map Value " . EzObjectUtils::toString(
                    $v
                ) . " Must Be Type Of $valueTypeExpect, But " . $valueType,
                0,
                GearIllegalArgumentException::class
            );
        }
        return true;
    }

    public static function isAllNotNull(...$obj) {
        foreach ($obj as $o) {
            if (is_null($o)) {
                return false;
            }
        }
        return true;
    }

    public static function isFalse($obj) {
        if (!is_bool($obj)) {
            Logger::warn("[EzDataUtils] method isFalse expect params is a boolean! but send:{}", EzObjectUtils::toString($obj));
            return false;
        }
        return !$obj;
    }

    public static function isScalar($data)
    {
        if (is_null($data)) {
            return true;
        }
        return is_scalar($data);
    }

    public static function isScalarType($dataType)
    {
        return in_array($dataType, self::$scalarTypeList)
            || in_array(self::$dataTypeMap[$dataType] ?? "", self::$scalarTypeList);
    }

    public static function dataTypeNameEquals($actual, $expect)
    {
        if ($actual === $expect) {
            return true;
        }
        if (is_null($expect) || is_null($actual)) {
            return false;
        }
        $actual = strtolower($actual);
        $expect = strtolower($expect);
        if ($actual === $expect) {
            return true;
        }
        $actualTrans = self::$dataTypeMap[$actual] ?? null;
        $expectTrans = self::$dataTypeMap[$expect] ?? null;
        return $actualTrans === $expectTrans;
    }

    /**
     * 判断传入的版本号是否符合要求
     * @param string $actual 实际的版本号 1.0.0
     * @param string $expect 版本号开闭集合 [1.0.0, 2.0.0) 或 [1.0.0, 2.0.0]
     * @return bool
     */
    public static function versionCheck($actual, $expect): bool {
        // 去掉空格
        $expect = trim($expect);

        $pattern = '/(>=|<=|>|<)/';
        // 存储匹配结果的数组
        $matches = [];
        // 使用 preg_match_all 查找所有匹配的比较符号
        preg_match($pattern, $expect, $matches);
        $matches = array_unique($matches);
        if (1 == count($matches)) {
            $left = current($matches);
            if (in_array($left, ['<', '>', '<=', '>='])) {
                return version_compare($actual, str_replace($left, "", $expect), $left);
            } else {
                DBC::throwEx("Invalid version range format.");
            }
        } else {
            $expectVersionArr = explode(',', substr($expect, 1, -1));
            $expectVersionArr = array_map('trim', $expectVersionArr);
            DBC::assertEquals(2, count($expectVersionArr), "Invalid version range format.");
            $left = $expect[0];
            $right = $expect[strlen($expect)-1];
            $compareLeft = $compareRight = true;
            if ($left == '[') {
                $compareLeft = version_compare($actual, $expectVersionArr[0], ">=");
            } elseif ($left == '(') {
                $compareLeft = version_compare($actual, $expectVersionArr[0], ">");
            } else {
                DBC::throwEx("Invalid version range format.");
            }
            if ($right == ']') {
                $compareRight = version_compare($actual, $expectVersionArr[1], "<=");
            } elseif ($right == ')') {
                $compareRight = version_compare($actual, $expectVersionArr[1], "<");
            } else {
                DBC::throwEx("Invalid version range format.");
            }
            return $compareLeft && $compareRight;
        }
    }
}
