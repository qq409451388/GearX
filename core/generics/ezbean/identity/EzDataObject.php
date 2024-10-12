<?php

/**
 * 标明数据类为数据对象
 */
interface EzDataObject
{
    /**
     * 格式化对象，支持递归
     * 默认返回 $this
     * @param $data
     * @return mixed
     */
    public function format(&$data);

    public function toString();

    public function toJson();
}
