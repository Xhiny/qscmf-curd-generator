<?php


namespace CurdGen\Mode;


class Factory
{

    public static function getInstance($type){
        $type = ucfirst($type);
        $cls = "CurdGen\\Mode\\{$type}Mode";
        if(class_exists($cls)){
            return new $cls();
        }
        else{
            throw new \Exception('未知类型');
        }
    }
}