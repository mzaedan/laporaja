<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_id' => 'required|exists:reports,id',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240', // 10MB max
            'status' => 'required|string|in:delivered,in_process,completed,rejected',
            'description' => 'required|string|min:3|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'report_id.required' => 'Report ID is required.',
            'report_id.exists' => 'The selected report does not exist.',
            'image.file' => 'The uploaded file must be a valid file.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, pdf, doc, docx.',
            'image.max' => 'The image may not be greater than 10MB.',
            'status.required' => 'Status is required.',
            'status.in' => 'The selected status is invalid.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 3 characters.',
            'description.max' => 'Description may not be greater than 1000 characters.',
        ];
    }
}
