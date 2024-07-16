<?php

class EzDateSerializer implements Serializer
{

    public function serialize($data): string
    {
        return $data->datetimeString();
    }
}
