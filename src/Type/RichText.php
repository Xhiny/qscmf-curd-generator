<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class RichText extends AbstractType implements IForm, IFormExtra {

    public function formParse()
    {
        if(isset($this->comment['oss']) && $this->comment['oss']){
            $extra_attr = "data-url=\$domain/Public/libs/ueditor/php/controller.php?oss=1&type=editor&url_suffix=\$url_suffix data-forcecatchremote='true'";
        }
        else{
            $extra_attr = "data-url=\$domain/Public/libs/ueditor/php/controller.php?type=editor&url_prefix=\$url_prefix data-forcecatchremote='true'";
        }

        $form_item = [
            'type' => Helper::wrap('ueditor'),
            'tip' => Helper::wrap(''),
            'options' => Helper::wrap(''),
            'extra_class' => Helper::wrap(''),
            'extra_attr' => Helper::wrap($extra_attr)
        ];

        return $form_item;
    }

    public function formExtraParse()
    {
        $str = <<<sample
            \$domain = HTTP_PROTOCOL . "://" . SITE_URL;

sample;


        if(isset($this->comment['oss']) && $this->comment['oss']){
            $str .= <<<sample
        \$url_suffix = "?x-oss-process=image/quality,q_90";
sample;
        }
        else{
            $str .= <<<sample
        \$url_prefix = U('/ip/q90', '', false, true) . '/' . U('/', '', false, true);
sample;
        }

        return $str;
    }
}