<?php
namespace CurdGen\Type;

use CurdGen\Helper;
use Illuminate\Support\Str;

class Select extends AbstractType implements ITable , IForm, ISave {


    public function formParse()
    {
        if(isset($this->comment['table']) && $table = $this->comment['table']){
            $show = $this->comment['show'];

            if(!$show){
                throw new \Exception('select type not found show');
            }

            $options = $this->genModelOptions($table, $show);
        }
        else{

            $list = $this->comment['list'];
            if(!$list){
                throw new \Exception('select type not found list');
            }

            $options = $this->genSelectOptions($list);
        }

        $form_item = [
            'type' => Helper::wrap('select'),
            'tip' => Helper::wrap(''),
            'options' => $options
        ];

        return $form_item;
    }

    protected function genModelOptions($table, $show){
        $model = ucfirst(Str::camel(str_replace(env('DB_PREFIX'), '', $table)));

        return "D('{$model}')->getField('id, {$show}', true)";
    }

    protected function genSelectOptions($list){
        $const_cls = Helper::guessDBCont($list);

        $list = ucfirst($list);

        return "{$const_cls}::get{$list}List()";
    }

    protected function genModelValue($table, $show){
        $model = ucfirst(Str::camel(str_replace(env('DB_PREFIX'), '', $table)));

        return Helper::wrap("D('{$model}')->getOneField(__data_id__,'{$show}')");
    }

    protected function genSelectValue($list){
        $const_cls = Helper::guessDBCont($list);

        $list = ucfirst($list);

        return Helper::wrap("{$const_cls}::get{$list}(__data_id__)");
    }

    public function tableParse()
    {
        if(isset($this->comment['table']) && $table = $this->comment['table']){
            $show = $this->comment['show'];

            if(!$show){
                throw new \Exception('select type not found show');
            }

            $value = $this->genModelValue($table, $show);
            $options = $this->genModelOptions($table, $show);
        }
        else{
            $list = $this->comment['list'];
            if(!$list){
                throw new \Exception('select type not found list');
            }

            $value = $this->genSelectValue($list);
            $options = $this->genSelectOptions($list);
        }


        $table_item = [
            'type' => Helper::wrap('fun'),
            'value' => $value
        ];

        if(isset($this->comment['save']) &&  $this->comment['save'] == 'true'){
            $table_item['type'] = Helper::wrap('select');
            $table_item['value'] = $options;
            $table_item['editable'] = 'true';
        }

        return $table_item;
    }

    public function saveParse()
    {
        return <<<sample
                \$save_data['{$this->column_set->COLUMN_NAME}'] = \$data['{$this->column_set->COLUMN_NAME}'][\$k];
sample;
    }
}