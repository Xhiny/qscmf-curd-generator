<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class District extends AbstractType implements ITable , IForm {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('district')
        ];

        return $form_item;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('fun'),
            'value' => Helper::wrap('getFullAreaByID(__data_id__)')
        ];

        return $table_item;
    }
}