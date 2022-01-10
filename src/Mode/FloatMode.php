<?php

namespace CurdGen\Mode;

class FloatMode extends AMode
{
    protected $mode = 'float';


    const DUMMY_ADD_MODAL = '{DummyAddModal}';

    const DUMMY_EDIT_MODAL = '{DummyEditModal}';


    public function addTopButton(&$stub, &$add_btn_placeholder = null)
    {
        list($add_top_modal_fun, $add_btn_placeholder, $operate) = $this->newButton('addTopButton');

        $operate = str_replace(self::DUMMY_ADD_MODAL, $add_top_modal_fun, $operate);

        return $operate;
    }

    public function editRightButton(&$stub, &$edit_btn_placeholder=null)
    {
        list($edit_top_modal_fun, $edit_btn_placeholder, $operate) = $this->newButton('editRightButton');

        $operate = str_replace(self::DUMMY_EDIT_MODAL, $edit_top_modal_fun, $operate);

        return $operate;
    }

    protected function newButton($button){
        $type = \CurdGen\Operate\Factory::getInstance($button, $this->mode);

        $res = [];
        if ($type instanceof \CurdGen\Operate\IOperate){
            $modal_fun = $type->modalFunParse();
            $table_btn_placeholder = $type->tableBtnPlaceholderParse();
            $operate = $type->operateParse();
            $res = [$modal_fun, $table_btn_placeholder, $operate];
        }

        if ($res){
            return $res;
        }

        return false;
    }
}