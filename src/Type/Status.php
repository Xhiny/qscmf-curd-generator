<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Status extends AbstractType implements IForm, ITable {

    public function formParse()
    {

        include_once LARA_DIR . '/../vendor/tiderjian/think-core/src/Library/Qscmf/Lib/DBCont.class.php';
        include_once LARA_DIR . '/../app/Gy_Library/DBCont.class.php';

        $list = $this->comment['list'];
        if(!$list){
            throw new \Exception('status type not found list');
        }

        $const_cls = $this->guessDBCont($list);

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

    protected function guessDBCont($list){


        $dbcont = [
            '\Qscmf\Lib\DBCont',
            '\Gy_Library\DBCont'
        ];

        $key = '_' . Str::snake($list);

        $dbcont_cls = '';

        foreach($dbcont as $v){
            $relection = new \ReflectionClass($v);
            $props = $relection->getProperties();

            $count = collect($props)->filter(function($item) use ($key){
                return $item->name == $key;
            })->count();

            if($count){
                $dbcont_cls = $v;
                break;
            }
        }

        return $dbcont_cls;
    }
}