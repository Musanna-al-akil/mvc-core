<?php

namespace Musanna\MvcCore;

use Musanna\MvcCore\db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName():string;
}