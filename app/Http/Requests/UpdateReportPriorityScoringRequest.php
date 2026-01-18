<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportPriorityScoringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can update priority scoring
        return auth()->check() && auth()->user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_score' => 'nullable|integer|min:1|max:5',
            'community_impact_score' => 'required|integer|min:1|max:5',
            'damage_level_score' => 'required|integer|min:1|max:5',
            'time_urgency_score' => 'required|integer|min:1|max:5',
            'scoring_notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'community_impact_score.required' => 'Community impact score is required.',
            'community_impact_score.integer' => 'Community impact score must be an integer.',
            'community_impact_score.min' => 'Community impact score must be at least 1.',
            'community_impact_score.max' => 'Community impact score must not exceed 5.',
            
            'damage_level_score.required' => 'Damage level score is required.',
            'damage_level_score.integer' => 'Damage level score must be an integer.',
            'damage_level_score.min' => 'Damage level score must be at least 1.',
            'damage_level_score.max' => 'Damage level score must not exceed 5.',
            
            'time_urgency_score.required' => 'Time urgency score is required.',
            'time_urgency_score.integer' => 'Time urgency score must be an integer.',
            'time_urgency_score.min' => 'Time urgency score must be at least 1.',
            'time_urgency_score.max' => 'Time urgency score must not exceed 5.',
            
            'scoring_notes.string' => 'Scoring notes must be a valid string.',
            'scoring_notes.max' => 'Scoring notes must not exceed 1000 characters.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'community_impact_score' => 'community impact score',
            'damage_level_score' => 'damage level score',
            'time_urgency_score' => 'time urgency score',
            'scoring_notes' => 'scoring notes'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Validation\ValidationException($validator, response()->json([
                'success' => false,
                'message' => 'Validation failed for priority scoring data.',
                'errors' => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
