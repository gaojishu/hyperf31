<?php

declare(strict_types=1);

namespace App\Request\Admin\Role;

use Hyperf\Validation\Request\FormRequest;

class RoleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => '',
            'name' => 'nullable|string|max:64',
            'remark' => 'nullable|string|max:500',
            'permission_key' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => '角色名称不能超过64个字符',
        ];
    }
}
