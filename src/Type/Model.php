<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Model extends AbstractType implements ITable , IForm {


    public function formParse()
    {
        $table = $this->comment['table'];

        $show = $this->comment['show'];

        if(!$table){
            throw new \Exception('model type not found table');
        }

        if(!$show){
            throw new \Exception('model type not found show');
        }

        $model = ucfirst(Str::camel(str_replace(env('DB_PREFIX'), '', $table)));

        $form_item = [
            'type' => Helper::wrap('select'),
            'tip' => Helper::wrap(''),
            'options' => "D('{$model}')->getField('id, {$show}', true)"
        ];

        return $form_item;
    }

    public function tableParse()
    {
        $table = $this->comment['table'];

        $show = $this->comment['show'];

        if(!$table){
            throw new \Exception('model type not found table');
        }

        if(!$show){
            throw new \Exception('model type not found show');
        }

        $model = ucfirst(Str::camel(str_replace(env('DB_PREFIX'), '', $table)));

        $table_item = [
            'type' => Helper::wrap('fun'),
            'value' => Helper::wrap("D('{$model}')->getOneField(__data_id__,'{$show}')")
        ];

        return $table_item;
    }
}