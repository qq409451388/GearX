<?php

use annotation\Anno;
use annotation\AnnoationElement;
use annotation\AnnoationRule;

trait EzReflectionTrait
{
    /**
     * @param Clazz<Anno> $annoClazz
     * @return AnnoationElement|null
     * @throws Exception
     */
    public function getAnnoation(Clazz $annoClazz) {
        return AnnoationRule::searchAnnoation($this, $annoClazz);
    }
}
