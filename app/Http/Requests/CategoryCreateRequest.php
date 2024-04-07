<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryCreateRequest extends FormRequest
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
            'name' => ['required', 'min:3', 'max:255'],
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
            'slug' => $this->name,
        ]);
    }
}
