<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ConferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Log the authorization check
        Log::info('Authorization check for ConferenceRequest.', [
            'user' => $this->user() ? $this->user()->id : 'guest',
        ]);

        // Allow all requests for simplicity; refine based on specific business logic if needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Log the application of validation rules
        Log::info('Applying validation rules for ConferenceRequest.', $this->all());

        return [
            'title' => 'required|string|max:100',
            'description' => 'required|string|min:10', // Enforcing a minimum length for description
            'date' => 'required|date|after:today', // Ensuring the date is in the future
            'address' => 'required|string|max:95',
            'participants' => 'required|integer|min:3|max:10000', // Adding a maximum limit for participants
        ];
    }

    /**
     * Customize the error messages for validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        // Log the error message customization
        Log::info('Customizing error messages for ConferenceRequest validation.');

        return [
            'title.required' => 'The conference title is mandatory.',
            'description.required' => 'Please provide a description for the conference.',
            'description.min' => 'The description must be at least 10 characters long.',
            'date.required' => 'A date for the conference is required.',
            'date.after' => 'The conference date must be in the future.',
            'address.required' => 'An address for the conference is required.',
            'participants.required' => 'Please specify the number of participants.',
            'participants.min' => 'There must be at least 3 participants.',
            'participants.max' => 'The number of participants cannot exceed 10,000.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->sanitizeTitle();
        $this->sanitizeAddress();
    }

    private function sanitizeTitle()
    {
        $this->merge(['title' => trim($this->title)]);
    }

    private function sanitizeAddress()
    {
        $this->merge(['address' => trim($this->address)]);
    }

}
