<?php


namespace CurdGen\Mode;


interface IMode
{

    public function tableColumn($columns_set);
    public function formColumn($columns_set);
    public function formExtra($columns_set);
    public function editColumn($columns_set);
    public function saveColumn($columns_set);
    public function addTopButton(&$stub, &$add_btn_placeholder = null);
    public function editRightButton(&$stub, &$table_btn_placeholder = null);

}