<?php

namespace App\Http\Requests;

use App\Enums\PlatformTypes;
use App\Enums\PostStatus;
use App\Enums\PublishingOptions;
use App\Rules\PlatformTextLimit;
use App\Rules\ScheduledTimeRequired;
use Doctrine\Inflector\Rules\English\Rules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePostRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'title' => ['required', 'max:255'],
            'content' => ['required', new PlatformTextLimit($this->input('platforms'))],
            'image' => ['nullable'],
            'platforms' => ['nullable', 'array'],
            'platforms.*' => ['nullable', 'exists:platforms,type'],
            'removedPlatforms' => ['nullable', 'array'],
            'removedPlatforms.*' => ['nullable', 'exists:platforms,type'],
            'scheduledDate' => [new ScheduledTimeRequired($this->input('publishOption'))],
            'publishOption' => ['required', new Enum(PostStatus::class)],
            'tags' => ['nullable', 'array'],
        ];

    }

    // custom validation that depends on whatever you want.
    function withValidator($validator)
    {
        // Add date rule to scheduledDate if there is a value in it.
        $validator->sometimes('scheduledDate', 'date', function ($input) {
            return !empty($input->scheduledDate);
        });

        // Image is required only if plaforms array contain instagram
        $validator->sometimes('image', 'required', function ($input) {
            return sizeof($input->platforms) > 0 && in_array(PlatformTypes::INSTAGRAM->value, $input->platforms);
        });
    }

    // Preparing anything you need before commiting the validation
    function prepareForValidation()
    {
        if ($this->has('publishOption')) {
            $this->merge([
                'publishOption' => ucfirst($this->input('publishOption')),
            ]);
        }
    }

}