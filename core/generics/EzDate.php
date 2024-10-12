<?php
final class EzDate extends DateObject implements EzSerializeDataObject
{
    public function getSerializeObj(): Clazz
    {
        return Clazz::get(EzDateSerializer::class);
    }

    public function getDeserializeObj(): Clazz
    {
        return Clazz::get(EzDateDeserializer::class);
    }

    /**
     * @return Serializer|null
     */
    public function getSerializer() {
        return $this->getSerializeObj()->getSerializer();
    }

    /**
     * @return Deserializer|null
     */
    public function getDeserializer()
    {
        return $this->getDeserializeObj()->getDeserializer();
    }

}
