<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class File extends AbstractType implements IForm {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('file')
        ];

        return $form_item;
    }

}