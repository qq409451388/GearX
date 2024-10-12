<?php

use annotation\AnnoationRule;
use annotation\annoconst\AnnoElementType;

class EzReflectionMethod extends ReflectionMethod
{
    use EzReflectionTrait;

    public function getAnnoationList() {
        return AnnoationRule::searchAnnoationFromDocument($this->getDocComment(), AnnoElementType::TYPE_METHOD);
    }

    /**
     * @return array<EzReflectionParameter>
     */
    public function getParameters():array {
        $parameters = parent::getParameters();
        $newParameters = [];
        foreach ($parameters as $parameter) {
            $newParameters[] = new EzReflectionParameter([$this->getDeclaringClass()->getName(), $this->getName()], $parameter->getName());
        }
        return $newParameters;
    }
}
