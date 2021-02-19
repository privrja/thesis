<?php

namespace App\Structure;

use App\Base\Message;

abstract class AbstractStructure {
    /** @var string|null */
    public $error = '';
    public abstract function checkInput(): Message;
    public abstract function transform(): AbstractTransformed;

}
