<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class RichText extends AbstractType implements IForm, IFormExtra {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('ueditor'),
            'tip' => Helper::wrap(''),
            'options' => Helper::wrap(''),
            'extra_class' => Helper::wrap(''),
            'extra_attr' => Helper::wrap("data-url=/Public/libs/ueditor/php/controller.php?url_prefix=\$url_prefix data-forcecatchremote='true'")
        ];

        return $form_item;
    }

    public function formExtraParse()
    {
        return <<<sample
               \$url_prefix = U('/ip/q90', '', false, true) . '/' . U('/', '', false, true);
sample;
    }
}