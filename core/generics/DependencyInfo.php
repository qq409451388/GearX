<?php
class DependencyInfo implements EzDataObject {
    public $name;

    public $version;

    /**
     * @var array<DependencyInfo> $dependencies
     */
    public $dependencies;
}