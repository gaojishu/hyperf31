<?php

declare(strict_types=1);

namespace App\Request\Admin\Admin;

use Hyperf\Validation\Request\FormRequest;

class AdminPageRequest extends FormRequest
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
            'params.id' => '',
            'params.username' => '',
            'params.nickname' => '',
            'params.mobile' => '',
            'params.email' => '',
            'params.created_at' => '',
            'params.updated_at' => '',
            'sort.id' => '',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
