<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class OccurrenceIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'base_name' => ['nullable', 'string', 'max:200'],
            'taxon_name' => ['nullable', 'string', 'max:200'],
            'base_id' => ['nullable', 'integer'],
            'lngmin' => ['nullable', 'numeric', 'between:-180,180'],
            'lngmax' => ['nullable', 'numeric', 'between:-180,180'],
            'latmin' => ['nullable', 'numeric', 'between:-90,90'],
            'latmax' => ['nullable', 'numeric', 'between:-90,90'],
            'cc' => ['nullable', 'string', 'max:100'],
            'continent' => ['nullable', 'string', 'max:50'],
            'interval' => ['nullable', 'string', 'max:100'],
            'min_ma' => ['nullable', 'numeric', 'min:0'],
            'max_ma' => ['nullable', 'numeric', 'min:0'],
            'envtype' => ['nullable', 'string'],
            'lithology' => ['nullable', 'string'],
            'idqual' => ['nullable', 'string', 'in:any,certain,uncertain'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'offset' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $filterKeys = [
                'base_name', 'taxon_name', 'base_id',
                'interval', 'min_ma', 'max_ma',
                'cc', 'continent',
                'lngmin', 'lngmax', 'latmin', 'latmax',
            ];

            $hasFilter = collect($filterKeys)->some(fn ($key) => $this->filled($key));

            if (! $hasFilter) {
                $validator->errors()->add(
                    'filters',
                    'At least one filter parameter is required to prevent returning all records.'
                );
            }
        });
    }
}
