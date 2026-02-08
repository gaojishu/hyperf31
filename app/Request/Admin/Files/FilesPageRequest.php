<?php

declare(strict_types=1);

namespace App\Request\Admin\Files;

use Hyperf\Validation\Request\FormRequest;

class FilesPageRequest extends FormRequest
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
            'params.category_id' => '',
            'params.type' => '',
            'params.created_at' => '',
            'sort.id' => '',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
