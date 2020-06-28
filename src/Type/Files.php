<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Files extends AbstractType implements IForm {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('files')
        ];

        return $form_item;
    }

}