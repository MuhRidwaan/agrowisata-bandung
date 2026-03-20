<?php

namespace App\Http\Requests;

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
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'vendor_id' => 'required|exists:vendors,id',
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
            'vendor_id.required' => 'Vendor harus dipilih.',
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
