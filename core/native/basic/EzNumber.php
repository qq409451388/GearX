<?php
abstract class EzNumber extends EzObject
{
    public abstract function longValue();
    public abstract function intValue();
    public abstract function floatValue();
    public abstract function doubleValue();
    public abstract function byteValue();
}
