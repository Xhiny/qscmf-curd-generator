<?php
namespace CurdGen\Type;

use CurdGen\Helper;

class Textarea extends AbstractType implements IForm, ISave, ITable {

    public function formParse()
    {
        $form_item = [
            'type' => Helper::wrap('textarea')
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
        if(isset($this->comment['save']) &&  $this->comment['save'] == 'true'){
            $table_item['type'] = Helper::wrap('textarea');
            $table_item['value'] = Helper::wrap('');
            $table_item['editable'] = 'true';
        }

        return $table_item ?? [];
    }
}