<?php

declare(strict_types=1);

namespace App\Request\Admin;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rules\Password;

class AdminCreateRequest extends FormRequest
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
            'username' => 'required|alpha_dash|min:6|max:32|alpha_dash',
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
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '用户名不能为空',
            'username.alpha_dash' => '用户名只能包含字母、数字、下划线、破折号',
            'username.min' => '用户名不能少于6个字符',
            'username.max' => '用户名不能超过32个字符',
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
