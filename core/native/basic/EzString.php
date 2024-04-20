<?php

final class EzString extends EzObject
{
    private $source;

    public function __construct($source) {
        $this->source = $source;
    }

    public function contain($nass){
        return false !== strstr($this->source, $nass);
    }

    public function equalsIgnoreCase($a, $b) {
        if (!is_string($a) || !is_string($b)) {
            return false;
        }
        return strtolower($a) == strtolower($b);
    }
}