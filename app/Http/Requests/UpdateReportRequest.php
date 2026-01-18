<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resident_id' => 'required|exists:residents,id',
            'report_category_id' => 'required|exists:report_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'address' => 'required|string',
            
            // Optional priority scoring fields (for admin updates)
            'category_score' => 'nullable|integer|min:1|max:5',
            'community_impact_score' => 'nullable|integer|min:1|max:5',
            'damage_level_score' => 'nullable|integer|min:1|max:5',
            'time_urgency_score' => 'nullable|integer|min:1|max:5',
            'scoring_notes' => 'nullable|string|max:1000'
        ];
    }
}
