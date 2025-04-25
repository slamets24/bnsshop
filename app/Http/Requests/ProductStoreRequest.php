<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'shopee_url' => 'nullable|string|url',
            'tokopedia_url' => 'nullable|string|url',
            'whatsapp_number' => 'nullable|string',
            // 'shopee_url' => ['nullable', 'string', 'url', 'regex:/^https:\/\/shopee\.co\.id\/.+/'],
            // 'tokopedia_url' => ['nullable', 'string', 'url', 'regex:/^https:\/\/www\.tokopedia\.com\/.+/'],
            // 'whatsapp_number' => ['nullable', 'string', 'regex:/^(\+62|62|0)[0-9]{9,12}$/'],
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
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
            'name' => 'Nama produk',
            'description' => 'Deskripsi',
            'price' => 'Harga',
            'stock' => 'Stok',
            'category_id' => 'Kategori',
            'shopee_url' => 'Link Shopee',
            'tokopedia_url' => 'Link Tokopedia',
            'whatsapp_number' => 'Nomor WhatsApp',
            'images' => 'Gambar produk',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi',
            'string' => ':attribute harus berupa teks',
            'max' => ':attribute tidak boleh lebih dari :max karakter',
            'numeric' => ':attribute harus berupa angka',
            'integer' => ':attribute harus berupa bilangan bulat',
            'min' => ':attribute minimal :min',
            'exists' => ':attribute yang dipilih tidak valid',
            'url' => ':attribute harus berupa URL yang valid',
            // 'url' => ':attribute harus berupa URL yang valid (dimulai dengan https://)',
            'unique' => ':attribute sudah digunakan',
            'images.required' => 'Gambar produk wajib diupload',
            'images.array' => 'Format gambar tidak valid',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Gambar harus berformat: jpeg, jpg, atau png',
            'images.*.max' => 'Ukuran gambar tidak boleh lebih dari 2MB',
            // 'shopee_url.regex' => 'Format URL Shopee tidak valid. Gunakan format: https://shopee.co.id/...',
            // 'tokopedia_url.regex' => 'Format URL Tokopedia tidak valid. Gunakan format: https://www.tokopedia.com/...',
            // 'whatsapp_number.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format: 08xx atau +62xx atau 62xx'
        ];
    }
}
