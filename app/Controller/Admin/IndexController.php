<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Admin;

use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController()]
class IndexController extends BaseController
{
    public function index()
    {
        return $this->setData(['aaa'])->apisucceed();
    }
}
