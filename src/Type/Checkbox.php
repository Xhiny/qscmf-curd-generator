<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Checkbox extends AbstractType implements ITable , IForm {


    public function formParse()
    {
        $list = $this->comment['list'];
        if(!$list){
            throw new \Exception('checkbox type not found list');
        }

        $options = $this->genCheckboxOptions($list);

        $form_item = [
            'type' => Helper::wrap('checkbox'),
            'tip' => Helper::wrap(''),
            'options' => $options
        ];

        return $form_item;
    }

    protected function genCheckboxOptions($list){
        $const_cls = Helper::guessDBCont($list);

        $list = ucfirst($list);

        return "{$const_cls}::get{$list}List()";
    }

    protected function genCheckboxValue($list){
        $const_cls = Helper::guessDBCont($list);

        $list = ucfirst($list);

        return Helper::wrap("{$const_cls}::get{$list}(__data_id__)");
    }

    public function tableParse()
    {
        $list = $this->comment['list'];
        if(!$list){
            throw new \Exception('checkbox type not found list');
        }

        $value = $this->genCheckboxOptions($list);


        $table_item = [
            'type' => Helper::wrap('select2'),
            'value' => $value
        ];

        return $table_item;
    }
}