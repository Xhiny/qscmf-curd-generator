<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Num extends AbstractType implements ITable , IForm, ISave, IValidate {

    public function validateParse()
    {
        return <<<p
        ['{$this->column_set->COLUMN_NAME}', 'double', '{$this->comment['title']}必须是数字', parent::VALUE_VALIDATE, 'regex'],
p
            . PHP_EOL;
    }

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('num')
        ];

        return $form_item;
    }

    public function saveParse()
    {
        return <<<sample
                \$save_data['{$this->column_set->COLUMN_NAME}'] = \$data['{$this->column_set->COLUMN_NAME}'][\$k];
sample;
    }

    public function tableParse()
    {
        $table_item = [
            'type' => Helper::wrap('num')
        ];

        if(isset($this->comment['save']) &&  $this->comment['save'] == 'true'){
            $table_item['value'] = Helper::wrap('');
            $table_item['editable'] = 'true';
        }

        return $table_item;
    }
}