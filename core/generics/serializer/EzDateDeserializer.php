<?php

class EzDateDeserializer implements Deserializer
{
    public function deserialize($data)
    {
        if (is_numeric($data)) {
            return new EzDate($data);
        } else {
            if (is_string($data)) {
                return EzDate::newFromString($data);
            } else {
                return null;
            }
        }
    }
}
