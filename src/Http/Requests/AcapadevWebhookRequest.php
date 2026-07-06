<?php

namespace Acapadev\Sdk\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcapadevWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // A autorização real já é feita pelo middleware VerifyWebhookSignature
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'event' => ['required', 'string'],
            'user' => ['required', 'array'],
            'user.id' => ['required', 'numeric'],
            'user.email' => ['required', 'email'],
            // Outros campos opcionais que possam vir na payload podem ser ignorados ou validados aqui
        ];
    }
}
