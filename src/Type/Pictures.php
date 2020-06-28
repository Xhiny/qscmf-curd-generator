<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Pictures extends AbstractType implements IForm {


    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('pictures')
        ];

        return $form_item;
    }
}