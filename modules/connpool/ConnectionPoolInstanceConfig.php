<?php
class ConnectionPoolInstanceConfig implements EzDataObject
{
    public $startMethod;
    public $args;

    public function getStartMethod() {
        return empty($this->startMethod) ? "getInstance" : $this->startMethod;
    }

    public function getArgs() {
        return empty($this->args) ? [] : $this->args;
    }
}
