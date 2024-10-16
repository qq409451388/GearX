<?php
class DateObject extends EzObject
{
    private $timeStamp;

    public const DAY_SEC = 86400;
    public const HOUR_SEC = 3600;
    public const MINUTE_SEC = 60;

    const FORMAT_DATETIME = 'Y-m-d H:i:s';
    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_TIME = 'H:i:s';

    public function __construct($timeStamp = null)
    {
        $this->timeStamp = is_null($timeStamp) ? time() : $timeStamp;
    }

    public static function newFromString($str) {
        return new DateObject(strtotime($str));
    }

    public function formatDate($format)
    {
        return date($format, $this->timeStamp);
    }

    public function dateString()
    {
        return $this->formatDate(self::FORMAT_DATE);
    }

    public function datetimeString()
    {
        return $this->formatDate(self::FORMAT_DATETIME);
    }

    public function offsetDay(int $day)
    {
        $this->timeStamp += $day * self::DAY_SEC;
        return $this;
    }

    public function offsetHour(int $hour)
    {
        $this->timeStamp += $hour * self::HOUR_SEC;
        return $this;
    }

    public function offsetMinute(int $min)
    {
        $this->timeStamp += $min * self::MINUTE_SEC;
        return $this;
    }

    public function offsetSec(int $sec)
    {
        $this->timeStamp += $sec;
        return $this;
    }

    public function __toString()
    {
        return $this->datetimeString();
    }

    public function afterNow() {
        return $this->timeStamp > time();
    }

    public function beforeNow() {
        return $this->timeStamp < time();
    }
}
