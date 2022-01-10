<?php

namespace CurdGen\Mode;

class StandardMode extends AMode
{
    protected $mode = 'standard';



    public function addTopButton(&$stub, &$add_btn_placeholder =null)
    {
        $operate = $this->newButton('addTopButton');

        return $operate;
    }

    public function editRightButton(&$stub, &$table_btn_placeholder=null)
    {

        $operate = $this->newButton('editRightButton');

        return $operate;
    }

    protected function newButton($button){
        $type = \CurdGen\Operate\Factory::getInstance($button, $this->mode);

        $res = [];
        if ($type instanceof \CurdGen\Operate\IOperate){
            $res = $type->operateParse();
        }

        if ($res){
            return $res;
        }

        return false;
    }
}