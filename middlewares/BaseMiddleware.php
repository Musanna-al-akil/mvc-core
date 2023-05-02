<?php

namespace Musanna\MvcCore\Middlewares;

abstract class BaseMiddleware
{
    abstract public function execute();
}