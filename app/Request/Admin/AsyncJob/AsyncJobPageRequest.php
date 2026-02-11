<?php

declare(strict_types=1);

namespace App\Request\Admin\AsyncJob;

use Hyperf\Validation\Request\FormRequest;

class AsyncJobPageRequest extends FormRequest
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
            'params.queue' => '',
            'params.job_class' => '',
            'params.status' => '',
            'params.created_at' => '',
            'params.updated_at' => '',
            'params.current' => '',
            'params.page_size' => '',
            'sort.id' => '',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
