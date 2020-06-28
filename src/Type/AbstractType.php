<?php
namespace CurdGen\Type;

abstract class AbstractType{

    protected $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }
}