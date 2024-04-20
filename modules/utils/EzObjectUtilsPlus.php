<?php
class EzObjectUtilsPlus
{
    /**
     * @param array|null $data
     * @param string $className
     * @return BaseDTO|EzIgnoreUnknow|object|null
     * @throws Exception
     */
    public static function createObject($data, string $className) {
        if (is_null($data)) {
            return null;
        }
        if (!is_object($data) && !is_array($data) && !EzDataUtils::isJson($data)
            && !is_subclass_of($className, EzSerializeDataObject::class)) {
            return null;
        }
        DBC::assertTrue(class_exists($className), "[EzObject] ClassName $className is not found!", 0, GearIllegalArgumentException::class);
        if (is_subclass_of($className, BaseDTO::class)) {
            return $className::create($data);
        } else if (is_subclass_of($className, EzSerializeDataObject::class)) {
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
            $dateItem = self::createObject($dateItem, $className);
        }
        return $data;
    }

    /**
     * @param $data
     * @param $className
     * @return BaseDTO|EzIgnoreUnknow|object|null
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
                        if (EzDataUtils::isScalar($item)) {
                            $list[$k] = $item;
                        } else {
                            $list[$k] = self::createObject($item, $propertyType);
                        }
                    }
                    $dItem = $list;
                    break;
                case "MAP":
                    $map = [];
                    foreach ($dItem as $k => $item) {
                        if (EzDataUtils::isScalar($item)) {
                            $map[$k] = $item;
                        } else {
                            $map[$k] = self::createObject($item, $propertyType[1]);
                        }
                    }
                    $dItem = $map;
                    break;
                case "OBJECT":
                    $dItem = self::createObject($dItem, $propertyType);
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
        if (EzDataUtils::isScalarType($propertyTypeMatched)) {
            $data = EzDataUtils::convertScalarToTrueType($data, $propertyTypeMatched);
            /*DBC::assertTrue(EzDataUtils::dataTypeNameEquals(gettype($data), $propertyTypeMatched),
                "[EzObject] Match data Fail! Type Must Be An $propertyTypeMatched, But ".gettype($data),
                0, GearIllegalArgumentException::class);*/
            return [null, null];
        }
        // 1. Array
        if ("array" == $propertyTypeMatched) {
            DBC::assertTrue(EzDataUtils::isArray($data), "[EzObject] Match data Fail! Type Must Be An Array, But ".gettype($data),
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
                $newData[EzDataUtils::convertScalarToTrueType($datak, $propertyType)] =
                    EzDataUtils::convertScalarToTrueType($datav, $propertyType2);
            }
            $data = $newData;
            DBC::assertTrue(EzDataUtils::isMap($data, $propertyType, $propertyType2), "[EzObject] Match data Fail! Type Must Be a Map, But ".gettype($data),
                0, GearIllegalArgumentException::class);
            return ["MAP", [$propertyType, $propertyType2]];
        }
        // 3. LIST
        preg_match("/array<\s*(?<propertyType>\w+)\s*>/", $propertyTypeMatched, $matched);

        $propertyType = $matched['propertyType'] ?? "";
        if (!empty($propertyType)) {
            DBC::assertTrue(EzDataUtils::isList($data), "[EzObject] Match data Fail! Type Must Be a Map, But ".gettype($data),
                0, GearIllegalArgumentException::class);
            if (EzDataUtils::isScalarType($propertyType)) {;
                foreach ($data as &$datum) {
                    $datum = EzDataUtils::convertScalarToTrueType($datum, $propertyType);
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
        $obj = EzDataUtils::ksortFromObject($obj);
        foreach ($obj as $key => $o) {
            if (is_array($o) || is_object($o)) {
                $obj[$key] = self::identityCode($o);
            }
        }
        return md5(serialize($obj));
    }
}