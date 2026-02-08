<?php

declare(strict_types=1);

namespace App\Request\Admin\FilesCategory;

use Hyperf\Validation\Request\FormRequest;

class FilesCategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => '',
            'name' => 'required|nullable|string|max:64',
            'remark' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
