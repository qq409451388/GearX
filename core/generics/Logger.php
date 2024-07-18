<?php
class Logger
{
    const LOG_PATH = DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
    //仅记录
    const TYPE_RECORD = 'record';
    //关键性数据储存
    const TYPE_DATA = 'data';

    private static $fontColors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];

    private static $backgroundColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

   public static function console(String $msg){
        if (Env::isWin()) {
            echo "[".date("Y-m-d H:i:s")."]".$msg.PHP_EOL;
        } else {
            self::info($msg);
            /*list($preClassField, $msg) = self::splitString($msg);
            $strings = [
                [$preClassField, "cyan"],
                [$msg, "black"]
            ];
            self::consolePlus($strings);*/
        }
   }

    /**
     * @param array<array{string, string, string}> | array<array{string, string}> | array<array{string}> $strings
     * @return void
     */
    public static function consolePlus($strings) {
        array_unshift($strings, ["  "]);
        array_unshift($strings, [date("Y-m-d H:i:s"), "light_gray"]);
        foreach ($strings as $string) {
            self::consoleColor($string[0], $string[1]??null, $string[2]??null);
        }
        echo PHP_EOL;
    }

    private static function consoleColor($string, $fontColor = null, $backgroundColor = null) {
        if (Env::isWin()) {
            $fontColor = $backgroundColor = null;
        }
        $coloredString = "";
        // Check if given foreground color found
        if (isset(self::$fontColors[$fontColor])) {
            $coloredString .= "\033[" . self::$fontColors[$fontColor] . "m";
        }
        // Check if given background color found
        if (isset(self::$backgroundColors[$backgroundColor])) {
            $coloredString .= "\033[" . self::$backgroundColors[$backgroundColor] . 'm';
        }
        // Add string and end coloring
        $coloredString .=  $string . "\033[0m";
        echo $coloredString;
    }

    public static function info($template, ...$args)
    {

        list($preClassField, $template) = self::splitString($template);
        $strings = [
            ['[INFO]', "green"],
            [$preClassField, "cyan"],
            [self::matchTemplate($template, $args), "black"]
        ];
        self::consolePlus($strings);

        if (Env::isProd()) {
            self::write("[INFO]".self::matchTemplate($template, $args), self::TYPE_RECORD);
        }
    }

    public static function warn($template, ...$args)
    {
        self::logException($args);

        list($preClassField, $template) = self::splitString($template);
        $strings = [
            ['[WARN]', "yellow"],
            [$preClassField, "cyan"],
            [self::matchTemplate($template, $args), "black"]
        ];
        self::consolePlus($strings);

        if (Env::isProd()) {
            self::write("[WARN]".self::matchTemplate($template, $args), self::TYPE_RECORD, date('Y-m-d')."_warn");
        }
    }

    public static function error($template, ...$args)
    {
        self::logException($args);
        list($preClassField, $template) = self::splitString($template);
        $strings = [
            ['[ERROR]', "red"],
            [$preClassField, "cyan"],
            [self::matchTemplate($template, $args), "black"]
        ];
        self::consolePlus($strings);

        if (Env::isProd()) {
            self::write("[ERROR]".self::matchTemplate($template, $args), self::TYPE_RECORD, date('Y-m-d')."_error");
        }
    }

    public static function exception($template, ...$args)
    {
        $template = '[Exception]'.$template;
        self::logException($args);
        $res = self::matchTemplate($template, $args);
        self::consoleColor($res.PHP_EOL, self::TYPE_RECORD, date('Y-m-d')."_exception");
        self::write($res, self::TYPE_RECORD, date('Y-m-d')."_exception");
    }

    private static function logException(&$args) {
        if (end($args) instanceof Error || end($args) instanceof Exception) {
            $exception = array_pop($args);
            Logger::exception("{} \n{}", $exception->getMessage(), $exception->getTraceAsString());
        }
    }

    public static function save($msg, $name)
    {
        self::write($msg, self::TYPE_DATA, $name);
    }

    public static function reSave($msg, $name)
    {
        self::clear(self::TYPE_DATA, $name);
        self::write($msg, self::TYPE_DATA, $name);
    }

    public static function get($name, $force = false) {
        if ($force && !is_file(self::generateFilePath(self::TYPE_DATA, $name))) {
            fopen(self::generateFilePath(self::TYPE_DATA, $name), "a+");
        }
        return self::read(self::TYPE_DATA, $name);
    }

    public static function saveAndShow($msg, $name){
        self::write($msg, self::TYPE_DATA, $name);
        self::console($msg);
    }

    private static function generateFilePath($type, $fileName) {
        $dirPath = self::LOG_PATH.$type.DIRECTORY_SEPARATOR;
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        $ext = '.log';
        return $dirPath.$fileName.$ext;
    }

    public static function clear($type, $fileName) {
        return @unlink(self::generateFilePath($type, $fileName));
    }

    private static function read($type, $fileName) {
        return file_get_contents(self::generateFilePath($type, $fileName));
    }

    private static function write($msg, $type, $fileName = '', $ext = ".log")
    {
        $dirPath = self::LOG_PATH.$type.DIRECTORY_SEPARATOR;
        if(!is_dir($dirPath))
        {
            mkdir($dirPath, 0777, true);
        }
        if(empty($fileName))
        {
            $fileName = date('Y-m-d');
        }
        $filePath = $dirPath.$fileName.$ext;
        $fp = fopen($filePath, 'a');
        if(self::TYPE_RECORD == $type)
        {
            $msg = date('Y/m/d H:i:s  ').$msg.PHP_EOL;
        }
        fwrite($fp, $msg);
        fclose($fp);
    }

    private static function matchTemplate($template, $args)
    {
        foreach($args as $arg)
        {
            $template = EzStringUtils::replaceOnce('{}', $arg, $template);
        }
        return $template;
    }

    public static function removeDir($type)
    {
        $dirPath = self::LOG_PATH.$type.DIRECTORY_SEPARATOR;
        if(is_dir($dirPath))
        {
            rmdir($dirPath);
        }
    }

    private static function splitString($str) {
        // 使用正则表达式找到第一个用 [] 包围的文字
        preg_match('/\[(.*?)\]/', $str, $matches);

        if (isset($matches[0])) {
            $found = $matches[0]; // 找到的字符串，包含 []
            $remaining = str_replace($found, '', $str, $count); // 移除找到的字符串后剩余的内容

            if ($count > 1) {
                $remaining = preg_replace('/\[(.*?)\]/', '', $str, 1); // 仅移除第一次出现的匹配
            }

            return [$found, $remaining];
        }

        return ["", $str]; // 如果没有找到，返回原始字符串
    }
}
