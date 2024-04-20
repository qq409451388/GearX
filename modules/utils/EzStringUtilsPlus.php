<?php
class EzStringUtilsPlus
{
    public static function hiddenTelNumber($phone) {
        $kindOf = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone);
        if ($kindOf == 1)
        {
            return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);

        }
        return  preg_replace('/(1[3456789]{1}[0-9])[0-9]{5}([0-9]{2})/i','$1*****$2',$phone);
    }

    public static function hiddenEmail($email)
    {
        $hiddenStr = '';
        if (EzCheckUtils::isEmail($email))
        {
            list($header, $footer) = explode('@', $email);
            $hiddenStr = substr($header, 0, 3)."****@".$footer;
        }
        return $hiddenStr;
    }

    public static function flattenString($obj) {
        if (empty($obj) || EzCheckUtils::isScalar($obj)) {
            return $obj;
        }
        foreach ($obj as $k => &$v) {
            if (EzCheckUtils::isString($v)) {
                $v = str_replace(array("\r\n", "\r", "\n"), "", $v);
            } else {
                $v = self::flattenString($v);
            }
        }
        return $obj;
    }
}