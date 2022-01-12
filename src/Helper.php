<?php
namespace CurdGen;

use Illuminate\Support\Str;

class Helper{

    static public function wrap($value){
        return "\"{$value}\"";
    }

    static public function guessDBCont($list){
        include_once LARA_DIR . '/../vendor/tiderjian/think-core/src/Library/Qscmf/Lib/DBCont.class.php';
        include_once LARA_DIR . '/../app/Gy_Library/DBCont.class.php';

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

    static public function getDummyModel($table_name){
        return ucfirst(Str::camel(str_replace(env('DB_PREFIX'), '', $table_name)));
    }
}