<?php
class ConfigurationAspect extends Aspect implements BuildAspect
{

    /**
     * 当Build发生时触发的方法
     * @return void
     */
    public function adhere(): void
    {
        if (Configuration::class == $this->getAnnoName()) {
            /**
             * @var DynamicProxy $configurationObj
             */
            $configurationObj = BeanFinder::get()->fetch($this->getAtClass()->getName());
            $config = Config::get($this->getValue()->getValue());
            $configurationObj->refreshObj(EzObjectUtils::create($config, $this->getAtClass()->getName()));
        }
    }
}
