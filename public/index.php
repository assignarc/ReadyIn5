<?php

use App\Kernel;
use App\TrueContainer;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

//$container = TrueContainer::buildContainer(dirname(__DIR__));

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
