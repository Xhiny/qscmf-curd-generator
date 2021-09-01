<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Radio extends AbstractType implements ITable , IForm {


    public function formParse()
    {
        $list = $this->comment['list'];
        if(!$list){
            throw new \Exception('radio type not found list');
        }

        $options = $this->genSelectOptions($list);

        $form_item = [
            'type' => Helper::wrap('radio'),
            'tip' => Helper::wrap(''),
            'options' => $options
        ];

        return $form_item;
    }

    protected function genSelectOptions($list){
        $const_cls = Helper::guessDBCont($list);

        $list = ucfirst($list);

        return "{$const_cls}::get{$list}List()";
    }

    protected function genSelectValue($list){
        $const_cls = Helper::guessDBCont($list);

        $list = ucfirst($list);

        return Helper::wrap("{$const_cls}::get{$list}(__data_id__)");
    }

    public function tableParse()
    {
        $list = $this->comment['list'];
        if(!$list){
            throw new \Exception('radio type not found list');
        }

        $value = $this->genSelectValue($list);


        $table_item = [
            'type' => Helper::wrap('fun'),
            'value' => $value
        ];

        return $table_item;
    }
}