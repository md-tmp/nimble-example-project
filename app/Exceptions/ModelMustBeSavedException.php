<?php
namespace App\Exceptions;

use Exception;

class ModelMustBeSavedException extends Exception
{
    public function __construct()
    {
        parent::__construct();
    }
}