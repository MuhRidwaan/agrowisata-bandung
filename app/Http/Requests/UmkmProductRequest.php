<?php

namespace App\Http\Requests;

use App\Models\PaketTour;
use Illuminate\Foundation\Http\FormRequest;

class UmkmProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'paket_tour_id' => [
                'required',
                'exists:paket_tours,id',
                function ($attribute, $value, $fail) {
                    $paketTour = PaketTour::find($value);

                    if (!$paketTour) {
                        return;
                    }

                    $user = $this->user();

                    if ($user && $user->hasRole('Vendor')) {
                        $vendorId = $user->vendor->id ?? null;
                        if ($paketTour->vendor_id !== $vendorId) {
                            $fail('Paket tour yang dipilih tidak valid untuk vendor ini.');
                        }
                    }

                    if ($this->filled('vendor_id') && (int) $this->input('vendor_id') !== (int) $paketTour->vendor_id) {
                        $fail('Vendor tidak sesuai dengan paket tour yang dipilih.');
                    }
                },
            ],
            'vendor_id' => 'nullable|exists:vendors,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'path_foto' => 'nullable|array',
            'path_foto.*' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'paket_tour_id.required' => 'Paket tour harus dipilih.',
            'paket_tour_id.exists' => 'Paket tour yang dipilih tidak valid.',
            'vendor_id.exists' => 'Vendor yang dipilih tidak valid.',
            'name.required' => 'Nama produk harus diisi.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'price.required' => 'Harga produk harus diisi.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'price.min' => 'Harga produk tidak boleh negatif.',
            'path_foto.*.file' => 'Foto harus berupa file.',
            'path_foto.*.image' => 'File harus berupa gambar.',
            'path_foto.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'path_foto.*.max' => 'Ukuran gambar maksimal 5MB.',
        ];
    }
}
