<?php
class EzCodecUtils
{
    public const EMPTY_JSON_OBJ = "{}";


    public static function encodeJson($obj){
        return json_encode($obj, JSON_UNESCAPED_UNICODE) ?? self::EMPTY_JSON_OBJ;
    }

    public static function decodeJson($json) {
        return empty($json) ? null : json_decode($json, true);
    }

    public static function decodeXml($xml) {
        if (!$xml instanceof SimpleXMLElement) {
            $xml = simplexml_load_string($xml);
        }
        $data = [];
        foreach ($xml->children() as $k => $child) {
            if (0 < $child->count()) {
                $data[$k] = self::decodeXml($child);
            } else {
                $data[$k] = strval($child);
            }
        }
        return $data;
    }

    public static function array2XML($array, $charset = 'gbk', $needCdata=true, $surRound = 'DOCUMENT') {
        $header = "<?xml version='1.0' encoding='".$charset."' ?>\n";
        $body = self::array2XMLBody($array, $needCdata);
        if (false == empty($surRound))
        {
            $body = "<".$surRound.">\n".$body."\n</".$surRound.">";
        }
        return $header.$body;
    }

    public static function array2XMLBody($array, $needCdata=true)
    {
        if(false == is_array($array))
        {
            return array();
        }
        $xml = "";
        foreach($array as $key=>$val)
        {
            if(is_numeric($key))
            {
                foreach( $val as $key2 => $value)
                {
                    if (false == is_numeric($key2))
                    {
                        $xml.="<$key2>";
                    }
                    if ($needCdata)
                    {
                        $xml .= is_array($value)?self::array2XMLBody($value, $needCdata):'<![CDATA['.$value.']]>'."\n";
                    }
                    else
                    {
                        $xml .= is_array($value)?self::array2XMLBody($value, $needCdata):$value."\n";
                    }
                    if (false == is_numeric($key2))
                    {
                        list($key2,)=explode(' ',$key2);
                        $xml.="</$key2>\n";
                    }
                }
            }
            else
            {
                $pre = "<$key>";
                if (is_array($val) && isset($val['@attributes']) && is_array($val['@attributes']) && false == empty($val['@attributes']))
                {
                    $pre = "<$key";
                    foreach ($val['@attributes'] as $attributeName => $attributeValue)
                    {
                        $pre .= " $attributeName='$attributeValue' ";
                    }
                    $pre .= "/>";
                    unset($val['@attributes']);
                    $key = '';
                }
                $xml.=$pre;
                if ($needCdata)
                {
                    $xml.=is_array($val)?self::array2XMLBody($val, $needCdata):'<![CDATA['.$val.']]>';
                }
                else
                {
                    $xml.=is_array($val)?self::array2XMLBody($val, $needCdata):$val;
                }
                if ($key)
                {
                    list($key,)=explode(' ',$key);
                    $xml.="</$key>\n";
                }
            }
        }

        return $xml;
    }


    public static function imgBase64Encode($img = '', $imgHtmlCode = true)
    {
        $file_content = file_get_contents($img);
        //如果是本地文件
        if (strpos($img, 'http') === false && !file_exists($img)) {
            return $img;
        }
        //获取文件内容
        var_dump($img);
        if ($file_content === false) {
            return $img;
        }
        $imageInfo = getimagesize($img);
        $prefiex = '';
        if ($imgHtmlCode) {
            $prefiex = 'data:' . $imageInfo['mime'] . ';base64,';
        }
        return $prefiex . (base64_encode($file_content));
    }

    public static function imgBase64EncodeSimple($picPath){
        return base64_encode(file_get_contents($picPath));
    }

    /**
     * 片base64解码
     * @param string $base64_image_content 图片文件流
     * @param bool $save_img    是否保存图片
     * @param string $path 文件保存路径
     * @return string
     */
    public static function imgBase64Decode($base64_image_content)
    {
        if (empty($base64_image_content)) {
            return '';
        }

        //匹配出图片的信息
        $match = preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result);
        if (!$match) {
            return '';
        }

        //解码图片内容
        $base64_image = str_replace($result[1], '', $base64_image_content);
        $file_content = base64_decode($base64_image);
        $file_type = $result[2];
        //如果没指定目录,则保存在当前目录下
        if (empty($path)) {
            return $file_content;
        }
        return empty($path) ? "" : $file_content;
    }
}
