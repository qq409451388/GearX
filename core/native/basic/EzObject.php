<?php
class EzObject implements EzDataObject
{
    public function hashCode() {
        $hashCode = 0;
        if (is_null($this)) {
            return $hashCode;
        }
        $str = serialize($this);
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $h = $hashCode << 5;
            $h -= $hashCode;
            $h += ord($str[$i]);
            $hashCode = $h;
            $hashCode &= 0xFFFFFF;
        }
        return $hashCode;
    }

    public function equals($object) {
        return $this->hashCode() == $object->hashCode();
    }

    public function toJson()
    {
        return EzCodecUtils::encodeJson($this);
    }

    public function format(&$data)
    {
        return $this;
    }

    public function __toString()
    {
        return $this->datetimeString();
    }

    public function toString()
    {
        return $this->datetimeString();
    }
}
