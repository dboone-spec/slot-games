<?php

/**
 * Date: 22.07.16
 * Time: 15:07
 * @author  Dmitriy Zhavoronkov <dimaz.lark@gmail.com>
 * @license MIT
 * @link    http://screensider.com/
 */
class Kohana_ORM_ClickHouse extends ORM
{
    /**
     * @inheritdoc
     */
    protected function _build_select()
    {
        return array_keys($this->table_columns());
    }
}
