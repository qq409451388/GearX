<?php
abstract class RuntimeAnnotation extends Anno
{
    public function constPolicy() {
        return AnnoPolicyEnum::POLICY_RUNTIME;
    }
}
