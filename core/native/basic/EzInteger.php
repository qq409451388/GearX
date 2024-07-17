<?php
final class EzInteger extends EzNumber {
    private $value;

    public function __construct($value)
    {
        $this->value = intval($value);
    }

    public function longValue()
    {
        return $this->value;
    }

    public function intValue()
    {
        return $this->value;
    }

    public function floatValue()
    {
        return floatval($this->value);
    }

    public function doubleValue()
    {
        return doubleval($this->value);
    }

    public function byteValue()
    {
        return 0;
    }

    public function equals($object) {
        if ($object instanceof EzInteger) {
            return $this->value === $object->intValue();
        }
        return false;
    }
}
