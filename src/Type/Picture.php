<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Picture extends AbstractType implements IForm, ITable {


    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('picture')
        ];

        return $form_item;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('picture')
        ];

        return $table_item;
    }
}