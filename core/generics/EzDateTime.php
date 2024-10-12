<?php
final class EzDateTime extends EzDate implements EzSerializeDataObject
{
    public function getSerializeObj(): Clazz
    {
        return Clazz::get(EzDateSerializer::class);
    }

    public function getDeserializeObj(): Clazz
    {
        return Clazz::get(EzDateDeserializer::class);
    }
}
