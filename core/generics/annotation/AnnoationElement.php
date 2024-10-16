<?php

class AnnoationElement
{
    public $annoName;
    /**
     * @var Anno $anno
     */
    public $anno;
    public $value;

    /**
     * @var AnnoElementType
     */
    public $at;

    /**
     * @var string 注解参数类型
     * @describe NORMAL：普通字符串  COMPLEX：json字符串
     */
    public $paramType;

    public static function create($n, $v, $a)
    {
        $obj = new AnnoationElement();
        $obj->annoName = $n;
        $obj->value = $v;
        $obj->at = $a;
        $obj->paramType = "NORMAL";
        return $obj;
    }

    public static function createComplex($n, $v, $a)
    {
        $obj = new AnnoationElement();
        $obj->annoName = $n;
        $obj->value = $v;
        $obj->at = $a;
        $obj->paramType = "COMPLEX";
        return $obj;
    }

    public function getValue(): Anno
    {
        if (!is_null($this->anno)) {
            return $this->anno;
        }
        $class = $this->annoName;
        /**
         * @var $anno Anno
         */
        $anno = new $class;
        $anno->combine($this->value);
        $this->anno = $anno;
        return $anno;
    }

    public function isNormal()
    {
        return "NORMAL" == $this->paramType;
    }
}
