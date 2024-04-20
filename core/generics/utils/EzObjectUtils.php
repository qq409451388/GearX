<?php
class EzObjectUtils
{
    private function __construct() {}

    public static function _dump(array $arr, $pos = 'default', $critical = []){
        $str = '';
        $eolHash = [
            'html' => '<br/>',
            'default' => "\n"
        ];
        $criticalHash = [
            'html' => '<font color="red" size="4" face="verdana">{temp}</font>',
            'default' => '[{$temp}]'
        ];
        $eol = $eolHash[$pos];
        foreach($arr as $k => $v){
            if(in_array($k, $critical)){
                $k = str_replace('{temp}', $k, $criticalHash[$pos]);
            }
            $str .= '['.$k.']  =>  '.$v.$eol;
        }
        return $str;
    }

    public static function toString($obj) {
        if (is_string($obj) || is_numeric($obj)) {
            return (string)$obj;
        } else {
            if ($obj instanceof EzDataObject) {
                if (function_exists("get_mangled_object_vars")) {
                    return EzCodecUtils::encodeJson(get_mangled_object_vars($obj));
                } else {
                    return EzCodecUtils::encodeJson(get_object_vars($obj));
                }
            } elseif (is_array($obj) || is_object($obj)) {
                return EzCodecUtils::encodeJson($obj);
            } elseif (is_resource($obj)) {
                return "[Resource]#" . ((int)$obj);
            } else {
                return "null";
            }
        }
    }

    /**
     * 标量转换真实类型
     * @param $data
     * @param string $dataType
     * @return mixed
     */
    public static function convertScalarToTrueType($data, string $dataType = null) {
        if (in_array($dataType, ["int", "integer", "Integer"])) {
            return intval($data);
        }
        if (in_array($dataType, ["float", "Float"])) {
            return floatval($data);
        }
        if (in_array($dataType, ["double", "Double"])) {
            return doubleval($data);
        }
        return strval($data);
    }

    public static function getFromObject($object, $key) {
        if (is_array($object)) {
            return $object[$key] ?? null;
        } else if (is_object($object)) {
            return $object->$key ?? null;
        } else {
            return null;
        }
    }

    /**
     * the left is the diff of obj1 with obj2, the right is the diff of obj2 with obj1
     * @param object|array $obj1
     * @param object|array $obj2
     * @return array<array<string>, array<string>>
     * @throws Exception
     */
    public static function compareObjectStruct($obj1, $obj2, $style = null) {
        $left = $right = [];
        DBC::assertFalse(self::isScalar($obj1),
            "[EzDataUtils] the function expect params 1 is not scalar, but given: ".self::toString($obj1));
        DBC::assertFalse(self::isScalar($obj2),
            "[EzDataUtils] the function expect params 2 is not scalar, but given: ".self::toString($obj2));
        DBC::assertEquals(gettype($obj1), gettype($obj2),
            "[EzDataUtils] analyseObject expect params type is same, but given: ".gettype($obj1)." and " . gettype($obj2));
        $keys1 = self::keys($obj1);
        $keys2 = self::keys($obj2);

        $diffLeft = array_diff($keys2, $keys1);
        $diffRight = array_diff($keys1, $keys2);
        $intersect = array_intersect($keys1, $keys2);
        $left = array_merge($left, $diffLeft);
        $right = array_merge($right, $diffRight);
        foreach ($intersect as $intersectKey) {
            $obj1Temp = self::getFromObject($obj1, $intersectKey);
            $obj2Temp = self::getFromObject($obj2, $intersectKey);
            if (EzCheckUtils::isScalar($obj1Temp)) {
                $obj1Temp = [];
            }
            if (EzCheckUtils::isScalar($obj2Temp)) {
                $obj2Temp = [];
            }
            list($leftTemp, $rightTemp) = self::compareObjectStruct($obj1Temp, $obj2Temp, $style);
            $leftTemp = self::appendKey($intersectKey, $leftTemp, $style);
            $rightTemp = self::appendKey($intersectKey, $rightTemp, $style);
            $left = array_merge($left, $leftTemp);
            $right = array_merge($right, $rightTemp);
        }
        return [$left, $right];
    }

    private static function appendKey($intersectKey, $temp, $style = null) {
        if (empty($temp)) {
            return [];
        }
        return array_map(function ($item) use ($intersectKey, $style) {
            return self::appendKeyStyle($intersectKey, $item, $style);
        }, $temp);
    }

    private static function appendKeyStyle($sourceKey, $appendKey, $style = null) {
        $style = empty($style) ? $style : strtoupper($style);
        switch ($style) {
            case "JAVA":
                return $sourceKey.".".$appendKey;
            case "PHP":
            case "PHP_OBJECT":
                return $sourceKey."->".$appendKey;
            default:
                return $sourceKey."|".$appendKey;
        }
    }

    public static function keys($obj) {
        if (is_array($obj)) {
            return array_keys($obj);
        } else if (is_object($obj)) {
            return array_keys(get_object_vars($obj));
        } else {
            return [];
        }
    }

    public static function createFromJson(string $json, string $className) {
        $data = EzCodecUtils::decodeJson($json);
        if (is_null($data)) {
            return null;
        }
        return self::create($data, $className);
    }

    public static function createObjectFromXml(string $xml, string $className) {
        $data = EzCodecUtils::decodeXml($xml);
        return self::create($data, $className);
    }

    /**
     * @param array|null $data
     * @param string $className
     * @return EzIgnoreUnknow|object|null
     * @throws Exception
     */
    public static function create($data, string $className) {
        if (is_null($data)) {
            return null;
        }
        if (!is_object($data) && !is_array($data) && !EzCheckUtils::isJson($data)
            && !is_subclass_of($className, EzSerializeDataObject::class)) {
            return null;
        }
        DBC::assertTrue(class_exists($className),
            "[EzObject] ClassName $className is not found!", 0, GearIllegalArgumentException::class);
        if (is_subclass_of($className, EzSerializeDataObject::class)) {
            $serializerClass = Clazz::get($className)->getDeserializer();
            if (is_null($serializerClass)) {
                Logger::error("[EzObject] class {} not found! for class:{}", $serializerClass, $className);
                return null;
            } else {
                return $serializerClass->deserialize($data);
            }
        } else {
            return self::createNormalObject($data, $className);
        }
    }

    public static function createObjectList($data, string $className) {
        if (empty($data)) {
            return $data;
        }
        foreach ($data as &$dateItem) {
            $dateItem = self::create($dateItem, $className);
        }
        return $data;
    }

    /**
     * @param $data
     * @param $className
     * @return EzIgnoreUnknow|object|null
     * @throws ReflectionException|GearIllegalArgumentException
     */
    private static function createNormalObject($data, $className) {
        $class = new $className;
        $refClass = new EzReflectionClass($class);
        $propertyAlias = self::analyseClassDocComment($refClass);
        foreach ($data as $key => $dItem) {
            try {
                $key = $propertyAlias[$key] ?? $key;
                $refProperty = $refClass->getProperty($key);
            }catch (ReflectionException $reflectionException) {
                $refProperty = null;
            }
            if (!$class instanceof EzIgnoreUnknow) {
                DBC::assertNonNull($refProperty, "[EzObject] PropertyName $key is not found From Class $className!",
                    0, GearIllegalArgumentException::class);
            } else {
                if (is_null($refProperty)) {
                    continue;
                }
            }
            $doc = $refProperty->getDocComment();
            list($struct, $propertyType) = self::analysePropertyDocComment($doc, $key, $dItem);
            switch ($struct) {
                case "LIST":
                    $list = [];
                    foreach ($dItem as $k => $item) {
                        if (EzCheckUtils::isScalar($item)) {
                            $list[$k] = $item;
                        } else {
                            $list[$k] = self::create($item, $propertyType);
                        }
                    }
                    $dItem = $list;
                    break;
                case "MAP":
                    $map = [];
                    foreach ($dItem as $k => $item) {
                        if (EzCheckUtils::isScalar($item)) {
                            $map[$k] = $item;
                        } else {
                            $map[$k] = self::create($item, $propertyType[1]);
                        }
                    }
                    $dItem = $map;
                    break;
                case "OBJECT":
                    $dItem = self::create($dItem, $propertyType);
                    break;
                case "ARRAY":
                default:
                    break;
            }

            if ($refProperty->isPublic()) {
                $refProperty->setValue($class, $dItem);
            } else {
                $refProperty->setAccessible(true);
                $refProperty->setValue($class, $dItem);
                $refProperty->setAccessible(false);
            }
        }
        return $class;
    }

    private static function analyseClassDocComment(EzReflectionClass $reflectionClass) {
        $propertyReflections = $reflectionClass->getProperties();
        $hash = [];
        foreach ($propertyReflections as $propertyReflection) {
            $annoItem = $propertyReflection->getAnnoation(Clazz::get(JsonProperty::class));
            if ($annoItem instanceof AnnoationElement) {
                $hash[$annoItem->value] = $propertyReflection->getName();
            }
        }
        return $hash;
    }

    /**
     * @param string $doc
     * @param mixed $data
     * @example {
            @var array $data
            @var ObjectClass $data
            @var array<string> $data
            @var array<ObjectClass> $data
            @var array<string, string> $data
            @var array<string, ObjectClass> $data
     * }
     * @return array<string, string>
     */
    private static function analysePropertyDocComment(string $doc, $column, &$data) {
        if (empty($doc)) {
            return [null, null];
        }
        preg_match("/\s+@required\s*/", $doc, $matched);
        DBC::assertTrue(empty($matched) || isset($data), "[EzObject] Required Column $column Check Fail! Data Must Be Set!",
            0, GearIllegalArgumentException::class);
        preg_match("/\*\s+@var\s+(?<propertyType>[a-zA-Z0-9\s<>,]+)(\r\n|\s+[\$][A-Za-z0-9]+\s*|\s*\*\/)/", $doc, $matched);
        $propertyTypeMatched = $matched['propertyType']??"";
        if (empty($propertyTypeMatched)) {
            return [null, null];
        }
        if (EzCheckUtils::isScalarType($propertyTypeMatched)) {
            $data = EzObjectUtils::convertScalarToTrueType($data, $propertyTypeMatched);
            /*DBC::assertTrue(EzDataUtils::dataTypeNameEquals(gettype($data), $propertyTypeMatched),
                "[EzObject] Match data Fail! Type Must Be An $propertyTypeMatched, But ".gettype($data),
                0, GearIllegalArgumentException::class);*/
            return [null, null];
        }
        // 1. Array
        if ("array" == $propertyTypeMatched) {
            DBC::assertTrue(EzCheckUtils::isArray($data), "[EzObject] Match data Fail! Type Must Be An Array, But ".gettype($data),
                0, GearIllegalArgumentException::class);
            return ["ARRAY", "array"];
        }
        // 2. MAP
        preg_match("/array<(?<propertyType>\w+)\s*,\s*(?<propertyType2>\w+)>/", $propertyTypeMatched, $matched);
        $propertyType = $matched['propertyType']??"";
        $propertyType2 = $matched['propertyType2']??"";
        if (!empty($propertyType2)) {
            $newData = [];
            foreach ($data as $datak => $datav) {
                $newData[EzObjectUtils::convertScalarToTrueType($datak, $propertyType)]
                    = EzObjectUtils::convertScalarToTrueType($datav, $propertyType2);
            }
            $data = $newData;
            DBC::assertTrue(EzCheckUtils::isMap($data, $propertyType, $propertyType2), "[EzObject] Match data Fail! Type Must Be a Map, But ".gettype($data),
                0, GearIllegalArgumentException::class);
            return ["MAP", [$propertyType, $propertyType2]];
        }
        // 3. LIST
        preg_match("/array<\s*(?<propertyType>\w+)\s*>/", $propertyTypeMatched, $matched);

        $propertyType = $matched['propertyType'] ?? "";
        if (!empty($propertyType)) {
            DBC::assertTrue(EzCheckUtils::isList($data), "[EzObject] Match data Fail! Type Must Be a Map, But ".gettype($data),
                0, GearIllegalArgumentException::class);
            if (EzCheckUtils::isScalarType($propertyType)) {;
                foreach ($data as &$datum) {
                    $datum = EzObjectUtils::convertScalarToTrueType($datum, $propertyType);
                }
            }
            return ["LIST", $propertyType];
        }
        // 4. OBJECT
        preg_match("/(?<propertyType>^(?!array)\w+)/", $propertyTypeMatched, $matched);
        $propertyType = $matched['propertyType']??"";
        if (!empty($propertyType)) {
            return ["OBJECT", $propertyType];
        }
        Logger::warn("[EzObject] May Has SomeThing Wrong With Match Object Type From Doc:{}", $doc);
        return [null, null];
    }

    /**
     * summary for a object
     * @param $obj
     * @return string
     */
    public static function identityCode($obj) {
        if (is_array($obj)) {
            return self::identityCodeForArray($obj);
        } else if (is_object($obj)) {
            return self::identityCodeForObject($obj);
        } else {
            return null;
        }
    }

    public static function identityCodeForArray(array $obj) {
        if (empty($obj)) {
            return null;
        }
        ksort($obj);
        foreach ($obj as $key => $o) {
            if (is_array($o) || is_object($o)) {
                $obj[$key] = self::identityCode($o);
            }
        }
        return md5(serialize($obj));
    }

    public static function identityCodeForObject(object $obj) {
        if (empty($obj)) {
            return null;
        }
        $obj = EzCollectionUtils::ksortFromObject($obj);
        foreach ($obj as $key => $o) {
            if (is_array($o) || is_object($o)) {
                $obj[$key] = self::identityCode($o);
            }
        }
        return md5(serialize($obj));
    }
}