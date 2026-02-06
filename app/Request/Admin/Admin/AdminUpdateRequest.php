<?php

declare(strict_types=1);

namespace App\Request\Admin\Admin;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rules\Password;

class AdminUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->max(32)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'nickname' => '',
            'mobile' => '',
            'email' => '',
            'permissionKey' => '',
            'disabledStatus' => '',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于8个字符',
            'password.max' => '密码不能超过32个字符',
            'password.letters' => '密码必须包含字母',
            'password.mixed' => '密码必须包含大小写字母',
            'password.numbers' => '密码必须包含数字',
            'password.symbols' => '密码必须包含特殊字符',
        ];
    }
}
