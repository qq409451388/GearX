<?php
class DependencyInfo extends EzObject implements EzDataObject {

    public $name;

    public $version;

    public $phpVersion;

    /**
     * @var array<DependencyInfo> $dependencies
     */
    public $dependencies;

    /**
     * @var array<string> $phpExtensions
     */
    public $phpExtensions;

    public function phpVersionCheck()
    {
        if (empty($this->phpVersion)) {
            return true;
        }
        return EzCheckUtils::versionCheck(PHP_VERSION, $this->phpVersion);
    }
}
