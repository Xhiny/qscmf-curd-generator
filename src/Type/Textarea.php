<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Textarea extends AbstractType implements IForm {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('textarea')
        ];

        return $form_item;
    }
}