<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'recipient_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'payment_method' => 'required|string',
            'payment_channel' => 'required_if:payment_method,bank_transfer,ewallet',
            'note' => 'nullable|string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih',
            'product_id.exists' => 'Produk tidak ditemukan',
            'quantity.required' => 'Jumlah wajib diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
            'recipient_name.required' => 'Nama penerima wajib diisi',
            'recipient_name.string' => 'Nama penerima harus berupa teks',
            'recipient_name.max' => 'Nama penerima maksimal 255 karakter',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi',
            'phone_number.string' => 'Nomor WhatsApp harus berupa teks',
            'phone_number.max' => 'Nomor WhatsApp maksimal 20 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'address.required' => 'Alamat wajib diisi',
            'address.string' => 'Alamat harus berupa teks',
            'province.required' => 'Provinsi wajib diisi',
            'province.string' => 'Provinsi harus berupa teks',
            'province.max' => 'Provinsi maksimal 100 karakter',
            'city.required' => 'Kota/Kabupaten wajib diisi',
            'city.string' => 'Kota/Kabupaten harus berupa teks',
            'city.max' => 'Kota/Kabupaten maksimal 100 karakter',
            'district.required' => 'Kecamatan wajib diisi',
            'district.string' => 'Kecamatan harus berupa teks',
            'district.max' => 'Kecamatan maksimal 100 karakter',
            'postal_code.required' => 'Kode pos wajib diisi',
            'postal_code.string' => 'Kode pos harus berupa teks',
            'postal_code.max' => 'Kode pos maksimal 10 karakter',
            'payment_method.required' => 'Metode pembayaran wajib dipilih',
            'payment_method.string' => 'Metode pembayaran tidak valid',
            'payment_channel.required_if' => 'Channel pembayaran wajib dipilih',
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
            'product_id' => 'Produk',
            'quantity' => 'Jumlah',
            'recipient_name' => 'Nama penerima',
            'phone_number' => 'Nomor WhatsApp',
            'email' => 'Email',
            'address' => 'Alamat',
            'province' => 'Provinsi',
            'city' => 'Kota/Kabupaten',
            'district' => 'Kecamatan',
            'postal_code' => 'Kode pos',
            'payment_method' => 'Metode pembayaran',
            'payment_channel' => 'Channel pembayaran',
            'note' => 'Catatan'
        ];
    }
}
