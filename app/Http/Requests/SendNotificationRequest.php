<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // We assume authentication is handled via middleware
    }

    public function rules(): array
    {
        return [
            'users' => 'required|array|min:1',
            'users.*' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'channels' => 'nullable|array',
            'channels.*' => 'string',
            'action_url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'priority' => 'nullable|in:urgent,high,normal,low',
        ];
    }
}
