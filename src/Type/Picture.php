<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Picture extends AbstractType implements IForm {


    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('picture')
        ];

        return $form_item;
    }
}