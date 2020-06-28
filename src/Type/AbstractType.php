<?php
namespace CurdGen\Type;

abstract class AbstractType{

    protected $comment;
    protected $column_set;

    public function __construct($comment, $column_set)
    {
        $this->comment = $comment;
        $this->column_set = $column_set;
    }
}