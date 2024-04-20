<?php

class EzStringUtils
{
    private function __construct() {}

    public static function convertToUnicode($str)
    {
        return self::convertToEncoding($str, 'UTF-8');
    }

    public static function convertToGbk($str)
    {
        //  return mb_convert_encoding($str, 'GBK', 'auto');
        return self::convertToEncoding($str, 'GBK');
    }

    public static function convertToEncoding($str, $toEncoding)
    {
        if ((! $str) || empty($str))
        {
            return $str;
        }

        $maybechset = mb_detect_encoding($str, array('UTF-8',  'GBK', 'ASCII', 'EUC-CN',  'CP936', 'UCS-2'));
        if (empty($maybechset))
        {
            $tmpstr = mb_convert_encoding($str, $toEncoding, 'UCS-2');
            $tmpchset = mb_detect_encoding($tmpstr, array('GBK'));
            if (strtoupper($tmpchset) == $toEncoding)
            {
                return $tmpstr;
            }
        }
        else if ($maybechset != $toEncoding)
        {
            return mb_convert_encoding($str, $toEncoding, $maybechset);
        }
        return $str;
    }

    public static function convertArrayToUnicode($var){
        if(is_array($var)){
            foreach($var as $k => $v){
                $var[$k] = self::convertArrayToUnicode($v);
            }
            return $var;
        }
        return self::convertToUnicodeNew($var);
    }

    public static function convertToUnicodeNew($str)
    {
        if(is_bool($str) || is_int($str)) return $str;
        $encodingOrder = ['ASCII', 'CP936', 'GBK', 'UTF-8', 'EUC-CN', 'UCS-2'];
        return self::convertToEncodingNew($str, 'UTF-8', $encodingOrder);
    }

    public static function convertToGbkNew($str)
    {
        if(is_bool($str) || is_int($str)) return $str;
        $encodingOrder = ['UTF-8', 'ASCII', 'CP936', 'GBK', 'EUC-CN', 'UCS-2'];
        return self::convertToEncodingNew($str, 'GBK', $encodingOrder);
    }

    protected static function convertToEncodingNew($str, $toEncoding, $recognitionArr = NULL)
    {
        if ((! $str) || empty($str))
        {
            return $str;
        }

        $encodingRecArr = ($recognitionArr === NULL) ? ['GBK', 'UTF-8'] : $recognitionArr;
        $maybechset = mb_detect_encoding($str, $encodingRecArr);
        if (empty($maybechset))
        {
            $tmpstr = mb_convert_encoding($str, $toEncoding, 'UCS-2');
            $tmpchset = mb_detect_encoding($tmpstr, array('GBK'));
            if (strtoupper($tmpchset) == $toEncoding)
            {
                return $tmpstr;
            }
        }
        else if ($maybechset != $toEncoding)
        {
            return mb_convert_encoding($str, $toEncoding, $maybechset);
        }
        return $str;
    }

    public static function truncate($string, $length, $postfix = '...')
    {
        $n = 0;
        $return = '';
        $isCode = false;
        $isHTML = false;
        for ($i = 0; $i < strlen($string); $i++)
        {
            $tmp1 = $string[$i];
            $tmp2 = ($i + 1 == strlen($string)) ? '' : $string[$i + 1];
            if ($tmp1 == '<')
            {
                $isCode = true;
            }
            elseif ($tmp1 == '&' && !$isCode)
            {
                $isHTML = true;
            }
            elseif ($tmp1 == '>' && $isCode)
            {
                $n--;
                $isCode = false;
            }
            elseif ($tmp1 == ';' && $isHTML)
            {
                $isHTML = false;
            }
            if (!$isCode && !$isHTML)
            {
                $n++;
                if (ord($tmp1) >= hexdec("0x81") && ord($tmp2) >= hexdec("0x40"))
                {
                    $tmp1 .= $tmp2;
                    $i++;
                    $n++;
                }
            }
            $return .= $tmp1;
            if ($n >= $length)
            {
                break;
            }
        }
        if ($n >= $length)
        {
            $return .= $postfix;
        }
        $html = preg_replace('/(^|>)[^<>]*(<?)/', '$1$2', $return);
        $html = preg_replace("/<\/?(br|hr|img|input|param)[^<>]*\/?>/i", '', $html);
        $html = preg_replace('/<([a-zA-Z0-9]+)[^<>]*>.*?<\/\1>/', '', $html);
        $count = preg_match_all('/<([a-zA-Z0-9]+)[^<>]*>/', $html, $matches);
        for ($i = $count - 1; $i >= 0; $i--)
        {
            $return .= '</' . $matches[1][$i] . '>';
        }
        return $return;
    }

    public static function cntrim($string)
    {
        return trim($string, "��\t\n\r ");
    }

    public static function convertEncoding($arr, $toEncoding, $fromEncoding='AUTO', $convertKey=false)
    {
        if (empty($arr) || $toEncoding == $fromEncoding)
        {
            return $arr;
        }
        if (is_array($arr))
        {
            $res = array();
            foreach ($arr as $key => $value)
            {
                if ($convertKey)
                {
                    $key = mb_convert_encoding($key, $toEncoding, $fromEncoding);
                }
                if (is_array($value))
                {
                    $value = self::convertEncoding($value, $toEncoding, $fromEncoding, $convertKey);
                }
                else
                {
                    $value = mb_convert_encoding($value, $toEncoding, $fromEncoding);
                }
                $res[$key] = $value;
            }
        }
        else
        {
            $res = mb_convert_encoding($arr, $toEncoding, $fromEncoding);
        }
        return $res;
    }

    public static function getRandomString($len)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);// ���������
        $output = "";
        for ($i=0; $i<$len; $i++)
        {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    public static function fixContent2Banjiao($str)
    {
        $arr = array(
            '��' => 'A', '��' => 'B', '��' => 'C', '��' => 'D', '��' => 'E',
            '��' => 'F', '��' => 'G', '��' => 'H', '��' => 'I', '��' => 'J',
            '��' => 'K', '��' => 'L', '��' => 'M', '��' => 'N', '��' => 'O',
            '��' => 'P', '��' => 'Q', '��' => 'R', '��' => 'S', '��' => 'T',
            '��' => 'U', '��' => 'V', '��' => 'W', '��' => 'X', '��' => 'Y',
            '��' => 'Z', '��' => 'a', '��' => 'b', '��' => 'c', '��' => 'd',
            '��' => 'e', '��' => 'f', '��' => 'g', '��' => 'h', '��' => 'i',
            '��' => 'j', '��' => 'k', '��' => 'l', '��' => 'm', '��' => 'n',
            '��' => 'o', '��' => 'p', '��' => 'q', '��' => 'r', '��' => 's',
            '��' => 't', '��' => 'u', '��' => 'v', '��' => 'w', '��' => 'x',
            '��' => 'y', '��' => 'z', '��' => '0', '��' => '1', '��' => '2',
            '��' => '3', '��' => '4', '��' => '5', '��' => '6', '��' => '7',
            '��' => '8', '��' => '9', '��' => ' '
        );

        foreach($arr as $key => $value)
        {
            $str = mb_ereg_replace($key, $value, $str);
        }
        return $str;
    }

    public static function replaceOnce($needle, $replace, $haystack) {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    public static function camelCase($str, $speartor, $type = 1){
        $newStr = '';
        $strArr = explode($speartor, $str);
        foreach($strArr as $s){
            $newStr .= ucfirst(strtolower($s));
        }
        return $type == 1 ? lcfirst($newStr) : $newStr;
    }

    /**
     * if $haystack contains $needle then return true else return false
     * @example containIgnoreCase("a", "abc") return true
     * @param $needle
     * @param $haystack
     * @return bool
     */
    public static function containIgnoreCase($needle, $haystack) {
        if (!is_string($haystack) || !is_string($needle)) {
            return false;
        }
        return false !== strstr($haystack, $needle);
    }

    public static function seemlike($a, $b, $level = false) {
        if (!is_string($a) || !is_string($b)) {
            return $level ? -1 : false;
        }
        $a = strtolower($a);
        $b = strtolower($b);

        $check1 = self::removeSpace($a) === self::removeSpace($b);
        if ($check1) {
            return $level ? 1 : true;
        }

        $aArr = explode(" ", $a);
        $bArr = explode(" ", $b);
        $check2 = !empty(array_intersect($aArr, $bArr));
        return $level ? ($check2 ? 2 : -1) : $check2;
    }

    public static function removeSpace($str) {
        if (is_null($str)) {
            return "";
        }
        return str_replace(" ", "", $str);
    }
}