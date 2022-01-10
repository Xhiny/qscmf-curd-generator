<?php
namespace CurdGen\Operate;

class Factory{

    public static function getInstance($type, $mode){
        $type = ucfirst($type);
        $cls = self::genClsNamespace($type, $mode);
        if(class_exists($cls)){
            return new $cls();
        }
        else{
            throw new \Exception('未知类型');
        }
    }

    protected static function genClsNamespace($type, $mode){
        $cls = '';
        if ($mode){
            $mode = ucfirst($mode);
            $cls = "CurdGen\\Mode\\Type\\{$mode}\\Operate\\{$type}";
        }

        if(!$cls || !class_exists($cls)){
            $cls = "CurdGen\\Operate\\{$type}";
        }

        return $cls;
    }
}