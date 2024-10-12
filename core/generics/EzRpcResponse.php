<?php

class EzRpcResponse implements EzDataObject
{
    public $errCode;
    public $data;
    public $msg;

    private const OK = 0;
    public const EMPTY_RESPONSE = EzCodecUtils::EMPTY_JSON_OBJ;

    public const SYSTEM_ERROR = 9999;
    public static $codeMap = [];

    public function __construct($data = [], $errCode = 0, $msg = ""){
        $this->errCode = $errCode;
        $this->data = $data;
        $this->msg = $msg;
    }

    public static function OK($data = null, $msg = ""){
        return (new self($data,self::OK, $msg));
    }

    public static function error($code, $msg = ""){
        if (empty($msg) && !empty(self::$codeMap[$code])) {
            return (new self(null, $code, self::$codeMap[$code]));
        }
        return (new self(null, $code, $msg));
    }

    public function toJson():string{
        if (is_array($this->data) || is_object($this->data)) {
            $this->format();
        }
        return EzCodecUtils::encodeJson($this)??self::EMPTY_RESPONSE;
    }

    public function format(&$data = null) {
        if ($data === null) {
            $data = $this->data;
        }
        foreach ($data as $k => &$v) {
            if ($v instanceof EzSerializeDataObject) {
                $v = $v->getSerializeObj()->getSerializer()->serialize($v);
            }
        }
    }

    public function toString () {
        return EzObjectUtils::toString(get_object_vars($this));
    }
}
