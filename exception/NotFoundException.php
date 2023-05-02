<?php

namespace Musanna\MvcCore\Exception;

class NotFoundException extends \Exception
{
    protected $message  = 'Page Not Found';
    protected $code     = 404;
}