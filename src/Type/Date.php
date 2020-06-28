<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Date extends AbstractType implements ITable , IForm {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('date')
        ];

        return $form_item;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('date')
        ];

        return $table_item;
    }
}