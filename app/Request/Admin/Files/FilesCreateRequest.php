<?php

declare(strict_types=1);

namespace App\Request\Admin\Files;

use App\Enum\Admin\AdminDisabledStatusEnum;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use Hyperf\Validation\Rules\Password;

class FilesCreateRequest extends FormRequest
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
            'file_list' => '',

        ];
    }

    public function messages(): array
    {
        return [];
    }
}
