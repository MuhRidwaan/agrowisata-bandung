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
            'paket_tour_ids' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    $paketTourIds = collect($value)
                        ->filter()
                        ->map(fn ($id) => (int) $id)
                        ->unique()
                        ->values();

                    if ($paketTourIds->isEmpty()) {
                        $fail('Minimal satu paket tour harus dipilih.');
                        return;
                    }

                    $paketTours = PaketTour::whereIn('id', $paketTourIds)->get(['id', 'vendor_id']);

                    if ($paketTours->count() !== $paketTourIds->count()) {
                        $fail('Satu atau lebih paket tour yang dipilih tidak valid.');
                        return;
                    }

                    $user = $this->user();

                    if ($user && $user->hasRole('Vendor')) {
                        $vendorId = $user->vendor->id ?? null;
                        if ($paketTours->contains(fn ($paketTour) => (int) $paketTour->vendor_id !== (int) $vendorId)) {
                            $fail('Paket tour yang dipilih tidak valid untuk vendor ini.');
                        }
                    }

                    $vendorIds = $paketTours->pluck('vendor_id')->unique();

                    if ($vendorIds->count() > 1) {
                        $fail('Produk UMKM hanya boleh dihubungkan ke paket tour dari vendor yang sama.');
                        return;
                    }

                    if ($this->filled('vendor_id') && (int) $this->input('vendor_id') !== (int) $vendorIds->first()) {
                        $fail('Vendor tidak sesuai dengan paket tour yang dipilih.');
                    }
                },
            ],
            'paket_tour_ids.*' => 'required|integer|distinct|exists:paket_tours,id',
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
            'paket_tour_ids.required' => 'Paket tour harus dipilih.',
            'paket_tour_ids.array' => 'Paket tour harus berupa daftar pilihan.',
            'paket_tour_ids.min' => 'Minimal satu paket tour harus dipilih.',
            'paket_tour_ids.*.required' => 'Paket tour harus dipilih.',
            'paket_tour_ids.*.integer' => 'Paket tour yang dipilih tidak valid.',
            'paket_tour_ids.*.distinct' => 'Paket tour yang sama tidak boleh dipilih lebih dari sekali.',
            'paket_tour_ids.*.exists' => 'Paket tour yang dipilih tidak valid.',
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
