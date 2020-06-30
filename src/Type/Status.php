<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Status extends AbstractType implements IForm, ITable {

    public function formParse()
    {
        $list = $this->comment['list'];
        if(!$list){
            throw new \Exception('status type not found list');
        }

        $const_cls = Helper::guessDBCont($list);

        $list = ucfirst($list);

        $form_item = [
            'type' => Helper::wrap('select'),
            'tips' => Helper::wrap(''),
            'options' => "{$const_cls}::get{$list}List()"
        ];

        return $form_item;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('status')
        ];

        return $table_item;
    }
}