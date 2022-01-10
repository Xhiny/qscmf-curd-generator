<?php

namespace CurdGen\Mode;


use CurdGen\Helper;
use CurdGen\Type\Factory;
use CurdGen\Type\IForm;
use CurdGen\Type\IFormExtra;
use CurdGen\Type\ISave;
use CurdGen\Type\ITable;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;

abstract class AMode implements IMode
{

    protected $mode;
    protected $table_item = [
        'name' => '',
        'title' => '',
    ];
    
    protected $form_item = [
        'name' => '',
        'type' => '',
        'title' => ''
    ];

    const DUMMY_MODEL = '{DummyModel}';
    const DUMMY_MODEL_TITLE = '{DummyModelTitle}';
    const DUMMY_TABLE_COLUMNS = '{DummyTableColumns}';
    const DUMMY_EDIT_COLUMNS = '{DummyEditColumns}';
    const DUMMY_FORM_COLUMNS = '{DummyFormColumns}';
    const DUMMY_SAVE_TOP_BUTTON = '{DummySaveTopButton}';
    const DUMMY_SAVE = '{DummySave}';
    const DUMMY_SAVE_COLUMNS = '{DummySaveColumns}';
    const DUMMY_FORM_EXTRA = '{DummyFormExtra}';

    const DUMMY_TABLE_DATA_LIST = '{DummyTableDataList}';
    const DUMMY_TABLE_BTN_PLACEHOLDER = '{DummyTableBtnPlaceholder}';

    const DUMMY_ADD = '{DummyAdd}';
    const DUMMY_EDIT = '{DummyEdit}';

    const DUMMY_ADD_TOP_BUTTON = '{DummyAddTopButton}';
    const DUMMY_EDIT_RIGHT_BUTTON = '{DummyEditRightButton}';

    public function exec($comment){
        $num = preg_match_all('/@(.+?)=(.+?);/', $comment, $match);
        $pair = [];
        for($i = 0; $i < $num; $i++){
            $pair[$match[1][$i]] = $match[2][$i];
        }

        if(!isset($pair['type'])){
            $pair['type'] = 'text';
        }

        return $pair;
    }

    public function tableColumn($column_set){
        $table_item = $this->table_item;

        if($column_set->COLUMN_KEY == 'PRI'){
            return false;
        }

        if($column_set->COLUMN_NAME == 'create_date'){
            return false;
        }

        $pair = self::exec($column_set->COLUMN_COMMENT);
        if(isset($pair['title'])){
            $table_item['title'] = Helper::wrap($pair['title']);
        }
        else{
            $table_item['title'] = Helper::wrap($column_set->COLUMN_NAME);
        }

        $table_item['name'] = Helper::wrap($column_set->COLUMN_NAME);

        $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
        if($type instanceof ITable){
            $res = $type->tableParse();
            $table_item = array_merge($table_item, $res);
        }

        $param_str = join(', ', $table_item);

        return <<<sample
            ->addTableColumn({$param_str})
sample;

    }

    public function formColumn($column_set){
        $form_item = $this->form_item;

        if($column_set->COLUMN_KEY == 'PRI'){
            return false;
        }

        if($column_set->COLUMN_NAME == 'create_date'){
            return false;
        }

        $form_item['name'] = Helper::wrap($column_set->COLUMN_NAME);

        $pair = self::exec($column_set->COLUMN_COMMENT);
        if(isset($pair['title'])){
            $form_item['title'] = Helper::wrap($pair['title']);
        }
        else{
            $form_item['title'] = Helper::wrap($column_set->COLUMN_NAME);
        }

        $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
        if($type instanceof IForm){
            $res = $type->formParse();
            $form_item = array_merge($form_item, $res);
        }

        $param_str = join(', ', $form_item);

        return <<<sample
                ->addFormItem({$param_str})
sample;
    }

    public function editColumn($column_set){
        if($column_set->COLUMN_KEY == 'PRI'){
            return false;
        }

        if($column_set->COLUMN_NAME == 'create_date'){
            return false;
        }

        return <<<sample
            \$ent['{$column_set->COLUMN_NAME}'] = \$data['{$column_set->COLUMN_NAME}'];
sample;
    }

    public function saveColumn($column_set){
        $pair = self::exec($column_set->COLUMN_COMMENT);

        $res = '';
        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
            if($type instanceof ISave && isset($pair['save']) && $pair['save'] == 'true'){
                $save_res = $type->saveParse();
                $res .= $save_res;
            }
        }
        if($res){
            return $res;
        }
        return false;
    }

    public function formExtra($column_set){
        $pair = self::exec($column_set->COLUMN_COMMENT);

        $res = '';
        if(isset($pair['type'])){
            $type = Factory::getInstance($pair['type'], $pair, $column_set, $this->mode);
            if($type instanceof IFormExtra){
                $res = $type->formExtraParse();
            }
        }
        if($res){
            return $res;
        }
        return false;
    }

    public function topButtonTable($button){
        $type = \CurdGen\Operate\Factory::getInstance($button, $this->mode);

        $res = [];
        if ($type instanceof \CurdGen\Operate\ITopButton){
            $res = $type->topButtonParse();
        }

        if ($res){
            return $res;
        }

        return false;
    }

    public function rightButtonTable($button){
        $type = \CurdGen\Operate\Factory::getInstance($button, $this->mode);

        $res = [];
        if ($type instanceof \CurdGen\Operate\IRightButton){
            $res = $type->rightButtonParse();
        }

        if ($res){
            return $res;
        }

        return false;
    }

    abstract public function addTopButton(&$stub, &$add_btn_placeholder = null);
    abstract public function editRightButton(&$stub, &$table_btn_placeholder=null);

    public function build(&$stub, $columns_set, $table_set){
        $dummy_model = Helper::getDummyModel($table_set[0]->TABLE_NAME);
        $key_pair = self::exec($table_set[0]->TABLE_COMMENT);

        if(!isset($key_pair['title'])){
            throw new InvalidArgumentException("请设置表 @title");
        }
        $dummy_model_title = $key_pair['title'];

        $dummy_table_columns = '';
        $dummy_form_columns = '';
        $dummy_edit_columns = '';
        $dummy_save_columns = '';
        $dummy_form_extra = '';

        foreach($columns_set as $column){
            $dump_table_str = self::tableColumn($column);
            if($dump_table_str !== false){
                $dummy_table_columns .= $dump_table_str . PHP_EOL;
            }

            $dump_form_str = self::formColumn($column);
            if($dump_form_str !== false){
                $dummy_form_columns .= $dump_form_str . PHP_EOL;
            }

            $dump_form_extra_str = self::formExtra($column);
            if($dump_form_extra_str !== false){
                $dummy_form_extra .= $dump_form_extra_str . PHP_EOL;
            }

            $dump_edit_str = self::editColumn($column);
            if($dump_edit_str !== false){
                $dummy_edit_columns .= $dump_edit_str . PHP_EOL;
            }

            $dummy_save_str = self::saveColumn($column);
            if($dummy_save_str !== false){
                $dummy_save_columns .= $dummy_save_str . PHP_EOL;
            }
        }

        if(strlen($dummy_save_columns) > 0){
            self::injectSaveTemplate($stub);
            self::injectSaveTopButton($stub);

            $stub = str_replace(self::DUMMY_SAVE_COLUMNS, trim($dummy_save_columns), $stub);
        }
        else{
            $stub = str_replace(self::DUMMY_SAVE, '', $stub);
            $stub = str_replace(self::DUMMY_SAVE_TOP_BUTTON, '', $stub);
        }

        self::buttonParse($stub, $table_btn_placeholder);

        if(strlen($table_btn_placeholder) > 0){
            self::injectTableDataListTemplate($stub);

            $stub = str_replace(self::DUMMY_TABLE_BTN_PLACEHOLDER, trim($table_btn_placeholder), $stub);
        }
        else{
            $stub = str_replace(self::DUMMY_TABLE_DATA_LIST, '', $stub);
        }

        $stub = str_replace(self::DUMMY_TABLE_COLUMNS, trim($dummy_table_columns), $stub);
        $stub = str_replace(self::DUMMY_EDIT_COLUMNS, trim($dummy_edit_columns), $stub);
        $stub = str_replace(self::DUMMY_FORM_COLUMNS, trim($dummy_form_columns), $stub);
        $stub = str_replace(self::DUMMY_MODEL, trim($dummy_model), $stub);
        $stub = str_replace(self::DUMMY_MODEL_TITLE, trim($dummy_model_title), $stub);
        $stub = str_replace(self::DUMMY_FORM_EXTRA, trim($dummy_form_extra), $stub);
    }

    protected function injectSaveTemplate(&$stub){
        $save_stub = (new Filesystem())->get(__DIR__ ."/../Stubs/save.stub");
        $stub = str_replace(self::DUMMY_SAVE, $save_stub, $stub);
    }

    protected function injectSaveTopButton(&$stub){
        $save_top_button = <<<template
->addTopButton('save', array('title' => '保存'))
template;

        $stub = str_replace(self::DUMMY_SAVE_TOP_BUTTON, $save_top_button, $stub);
    }

    protected function injectTableDataListTemplate(&$stub){
        $table_data_list = (new Filesystem())->get(__DIR__ ."/../Stubs/tableDataList.stub");
        $stub = str_replace(self::DUMMY_TABLE_DATA_LIST, $table_data_list, $stub);
    }

    protected function buttonParse(&$stub, &$table_btn_placeholder=null){
        $stub = str_replace(self::DUMMY_ADD_TOP_BUTTON, trim($this->topButtonTable('addTopButton')), $stub);
        $stub = str_replace(self::DUMMY_EDIT_RIGHT_BUTTON, trim($this->rightButtonTable('editRightButton')), $stub);

        $add = $this->addTopButton($stub, $add_btn_placeholder);
        $stub = str_replace(self::DUMMY_ADD, $add, $stub);
        $edit = $this->editRightButton($stub, $edit_btn_placeholder);
        $stub = str_replace(self::DUMMY_EDIT, $edit, $stub);

        $table_btn_placeholder = implode(PHP_EOL, array_filter([$add_btn_placeholder,$edit_btn_placeholder]));

    }

}