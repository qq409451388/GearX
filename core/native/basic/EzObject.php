<?php
class EzObject
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

    /**
     * 将对象转为可读的字符串
     * @param $object
     * @return string
     */
    public function toString($object):string {
        if(is_string($object) || is_numeric($object)){
            return strval($object);
        } elseif (is_object($object)) {
            return "[.".get_class($object).".]".json_encode($object);
        } elseif (is_array($object)) {
            return json_encode($object);
        } elseif (is_bool($object)) {
            return $object ? "[boolean:true]" : "[boolean:false]";
        } else {
            return strval($object);
        }
    }
}