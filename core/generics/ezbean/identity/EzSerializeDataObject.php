<?php

/**
 * 必须实现Serializer Deserializer的对象
 * @link EzBeanUtils
 */
interface EzSerializeDataObject extends EzDataObject
{
    /**
     * @return Clazz<Serializer>
     */
    public function getSerializeObj():Clazz;

    /**
     * @return Clazz<DeSerializer>
     */
    public function getDeserializeObj():Clazz;
}
