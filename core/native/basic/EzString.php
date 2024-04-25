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

    public function length(): int {
        return count($this->source);
    }

    public function charAt($pos) {
        return $this->source[$pos]??null;
    }

    public function equals($object) {
        return $object === $this->source;
    }

    public function substring($pos, $len = null) {
        return substr($this->source, $pos, $len);
    }

    public function replace($old, $new) {
        return str_replace($old, $new, $this->source);
    }

    public function toLowerCase() {
        return strtolower($this->source);
    }

    public function toUpperCase() {
        return strtoupper($this->source);
    }

    public function trim() {
        return trim($this->source);
    }

    public function startsWith($prefix) {
        return 0 === strpos($this->source, $prefix);
    }

    public function endsWith($suffix) {
        return $suffix === substr($this->source, -strlen($suffix));
    }

    public function split($delimiter) {
        return explode($delimiter, $this->source);
    }

    public function isEmpty() {

        return '' === trim($this->source);
    }

    public function isNotEmpty() {
        return '' !== trim($this->source);
    }

    public function isBlank() {
        return empty($this->source);
    }

    public function isNotBlank() {
        return !empty($this->source);
    }
}