<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class RichText extends AbstractType implements IForm {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('ueditor')
        ];

        return $form_item;
    }
}