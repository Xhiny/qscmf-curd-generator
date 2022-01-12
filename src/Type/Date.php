<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Date extends AbstractType implements ITable , IForm, IAuto , ISave{

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('date')
        ];

        return $form_item;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('date')
        ];

        if(isset($this->comment['save']) &&  $this->comment['save'] == 'true'){
            $table_item['value'] = Helper::wrap('');
            $table_item['editable'] = 'true';
        }

        return $table_item;
    }

    public function saveParse()
    {
        return <<<sample
                \$save_data['{$this->column_set->COLUMN_NAME}'] = strtotime(\$data['{$this->column_set->COLUMN_NAME}'][\$k]);
sample;
    }

    public function autoParse()
    {
        return <<<p
        ['{$this->column_set->COLUMN_NAME}', 'strtotime', parent::MODEL_BOTH, 'function'],
p
            . PHP_EOL;
    }
}