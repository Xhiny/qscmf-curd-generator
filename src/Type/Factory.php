<?php
namespace CurdGen\Type;

class Factory{

    public static function getInstance($type, $comment, $column_set, $mode = ''){
        $type = ucfirst($type);
        $cls = self::genClsNamespace($type, $mode);
        if(class_exists($cls)){
            return new $cls($comment, $column_set);
        }
        else{
            throw new \Exception('未知类型');
        }
    }

    protected static function genClsNamespace($type, $mode){
        $cls = '';
        if ($mode){
            $mode = ucfirst($mode);
            $cls = "CurdGen\\Mode\\Type\\{$mode}\\Column\\Type\\{$type}";
        }

        if(!$cls || !class_exists($cls)){
            $cls = "CurdGen\\Type\\{$type}";
        }

        return $cls;
    }
}