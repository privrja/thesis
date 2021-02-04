<?php

namespace App\Structure;

use App\Base\Message;

abstract class AbstractStructure {
    public abstract function checkInput(): Message;
    public abstract function transform(): AbstractTransformed;

}
