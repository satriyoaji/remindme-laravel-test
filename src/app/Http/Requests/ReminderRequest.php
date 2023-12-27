<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ReminderRequest extends FormRequest
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
        if ($this->getMethod() == 'POST') {
            $rules = [
                'title' => ['required', 'max:255'],
                'description' => ['required', 'min:2'],
                'remind_at' => ['required', 'integer'],
                'event_at' => ['required', 'integer'],
            ];
        } else {
            $rules = [
                'title' => ['max:255'],
                'description' => ['min:2'],
                'remind_at' => ['integer'],
                'event_at' => ['integer'],
            ];
        }

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        Log::error(json_encode($validator->errors()));
        throw new HttpResponseException(response()->json($this->badRequest(), 400));
    }
}
