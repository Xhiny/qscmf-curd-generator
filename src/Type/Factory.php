<?php
namespace CurdGen\Type;

class Factory{

    public static function getInstance($type, $comment, $column_set){
        $type = ucfirst($type);
        $cls = "CurdGen\\Type\\{$type}";
        if(class_exists($cls)){
            return new $cls($comment, $column_set);
        }
        else{
            throw new \Exception('未知类型');
        }
    }
}