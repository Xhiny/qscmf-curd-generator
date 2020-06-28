<?php
namespace CurdGen\Commands;

use CurdGen\Parser;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CurdGenCommand extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qscmf:curd-gen {table_name : The name of table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auto build curd code feel like rocket';

    const DUMMY_MODEL = '{DummyModel}';
    const DUMMY_MODEL_TITLE = '{DummyModelTitle}';
    const DUMMY_TABLE_COLUMNS = '{DummyTableColumns}';
    const DUMMY_EDIT_COLUMNS = '{DummyEditColumns}';
    const DUMMY_FORM_COLUMNS = '{DummyFormColumns}';

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }


    public function handle(){
        $table = $this->input->getArgument('table_name');

        $schema = env('DB_DATABASE');
        $columns_res = DB::select("select * from information_schema.columns where TABLE_SCHEMA='{$schema}' and TABLE_NAME='{$table}'");
        $table_res = DB::select("select * from information_schema.tables where TABLE_SCHEMA='{$schema}' and TABLE_NAME='{$table}'");

        $stub = $this->getStub('controller');

        $path = $this->populateControllerStub($stub, $columns_res, $table_res);
        $path_arr = explode('/', $path);
        if($this->files->exists($path)){
            $confirm = $this->confirm( $path_arr[count($path_arr) - 1]. '已经存在，确定要覆盖吗');
            $confirm && $this->makeFile($path, $stub);
        }
        else{
            $this->makeFile($path, $stub);
        }

        $stub = $this->getStub('model');

        $path = $this->populateModelStub($stub, $columns_res, $table_res);
        $path_arr = explode('/', $path);
        if($this->files->exists($path)){
            $confirm = $this->confirm( $path_arr[count($path_arr) - 1]. '已经存在，确定要覆盖吗');
            $confirm && $this->makeFile($path, $stub);
        }
        else{
            $this->makeFile($path, $stub);
        }

    }

    protected function makeFile($path, $stub){
        $this->files->put($path, $stub);
        $path_arr = explode('/', $path);
        $this->info($path_arr[count($path_arr) - 1] . ' 成功生成');
    }


    protected function getStub($stub_type){
        return $this->files->get(__DIR__ ."/../Stubs/{$stub_type}.stub");
    }

    protected function getDummyModel($table_name){
        return ucfirst(Str::camel(str_replace(env('DB_PREFIX'), '', $table_name)));
    }

    protected function populateModelStub(&$stub, $columns_set, $table_set){
        $dummy_model = $this->getDummyModel($table_set[0]->TABLE_NAME);

        $stub = str_replace(self::DUMMY_MODEL, trim($dummy_model), $stub);

        return LARA_DIR . '/../app/Common/Model/' . $dummy_model . 'Model.class.php';
    }

    protected function populateControllerStub(&$stub, $columns_set, $table_set){
        $dummy_model = $this->getDummyModel($table_set[0]->TABLE_NAME);

        $key_pair = Parser::exec($table_set[0]->TABLE_COMMENT);

        if(!isset($key_pair['title'])){
            throw new InvalidArgumentException("请设置表 @title");
        }
        $dummy_model_title = $key_pair['title'];

        $dummy_table_columns = '';
        $dummy_form_columns = '';
        $dummy_edit_columns = '';
        foreach($columns_set as $column){
            $dump_table_str = Parser::tableColumn($column);
            if($dump_table_str !== false){
                $dummy_table_columns .= $dump_table_str . PHP_EOL;
            }

            $dump_form_str = Parser::formColumn($column);
            if($dump_form_str !== false){
                $dummy_form_columns .= $dump_form_str . PHP_EOL;
            }

            $dump_edit_str = Parser::editColumn($column);
            if($dump_edit_str !== false){
                $dummy_edit_columns .= $dump_edit_str . PHP_EOL;
            }
        }

        $stub = str_replace(self::DUMMY_MODEL, trim($dummy_model), $stub);
        $stub = str_replace(self::DUMMY_MODEL_TITLE, trim($dummy_model_title), $stub);
        $stub = str_replace(self::DUMMY_TABLE_COLUMNS, trim($dummy_table_columns), $stub);
        $stub = str_replace(self::DUMMY_EDIT_COLUMNS, trim($dummy_edit_columns), $stub);
        $stub = str_replace(self::DUMMY_FORM_COLUMNS, trim($dummy_form_columns), $stub);

        return LARA_DIR . '/../app/Admin/Controller/' . $dummy_model . 'Controller.class.php';
    }
}