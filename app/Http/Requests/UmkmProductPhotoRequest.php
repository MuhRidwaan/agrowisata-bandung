<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UmkmProductPhotoRequest extends FormRequest
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
            'umkm_product_id' => [
                'required',
                'exists:umkm_products,id',
                function ($attribute, $value, $fail) {
                    if (auth()->user()->hasRole('Vendor')) {
                        $exists = \App\Models\UmkmProduct::where('id', $value)
                            ->where('vendor_id', auth()->user()->vendor->id ?? null)
                            ->exists();
                        if (!$exists) {
                            $fail('Produk UMKM yang dipilih tidak valid.');
                        }
                    }
                },
            ],
            'path_foto' => 'required|array|min:1',
            'path_foto.*' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'umkm_product_id.required' => 'ID Produk UMKM harus diisi.',
            'umkm_product_id.exists' => 'Produk UMKM yang dipilih tidak valid.',
            'path_foto.required' => 'Minimal satu foto harus diupload.',
            'path_foto.array' => 'Foto harus berupa array.',
            'path_foto.min' => 'Minimal satu foto harus diupload.',
            'path_foto.*.required' => 'Foto harus diisi.',
            'path_foto.*.file' => 'Foto harus berupa file.',
            'path_foto.*.image' => 'File harus berupa gambar.',
            'path_foto.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'path_foto.*.max' => 'Ukuran gambar maksimal 5MB.',
        ];
    }
}
