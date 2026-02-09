<?php

declare(strict_types=1);

namespace App\Request\Admin\Notice;

use Hyperf\Validation\Request\FormRequest;

class NoticePageRequest extends FormRequest
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
            'params.title' => '',
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
