<?php

declare(strict_types=1);

namespace App\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;


#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD,)]
class PermissionAnnotation extends AbstractAnnotation
{

    public function __construct(public string $code, public ?string $remark) {}
}
