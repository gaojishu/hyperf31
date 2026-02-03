<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Util\HttpResponse\HttpResponse;

class BaseController extends AbstractController
{
    use HttpResponse;
}
