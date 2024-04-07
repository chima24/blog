<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:6', 'max:255'],
            'body' => ['required', 'min:150'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    /**
     * Add additional fields to validated fields.
     *
     * @return array
     */
    public function validated(): array
    {
        return array_merge(parent::validated(), [
            'slug' => $this->title,
        ]);
    }
}
