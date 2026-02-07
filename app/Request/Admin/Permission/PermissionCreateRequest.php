<?php

declare(strict_types=1);

namespace App\Request\Admin\Permission;

use App\Enum\Admin\PermissionTypeEnum;
use Hyperf\Validation\Request\FormRequest;

class PermissionCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:64',
            'icon' => 'nullable|string|max:128',
            'parent_id' => 'nullable|integer|min:0',
            'path' => 'nullable|string|max:255',
            'remark' => 'nullable|string|max:255',
            'sort' => 'nullable|integer|min:0',
            'type' => 'nullable|integer|in:' . implode(',', PermissionTypeEnum::values()),
            'code' => 'nullable|string|max:64',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '权限名称不能为空',
            'name.max' => '权限名称不能超过64个字符',
            'type.in' => '权限类型无效',
        ];
    }
}
