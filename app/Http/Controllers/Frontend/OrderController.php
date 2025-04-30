<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkout(Product $product)
    {
        return Inertia::render('Frontend/Checkout', [
            'product' => $product->load('category'),
            'paymentMethods' => [
                ['id' => 'bank_transfer', 'name' => 'Transfer Bank'],
                ['id' => 'ewallet', 'name' => 'E-Wallet'],
                ['id' => 'cod', 'name' => 'Cash on Delivery']
            ]
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
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
            ]);

            DB::beginTransaction();

            // Get product
            $product = Product::findOrFail($request->product_id);

            // Check stock
            if ($product->stock < $request->quantity) {
                throw new \Exception("Stok produk tidak mencukupi");
            }

            // Calculate total
            $total = $product->price * $request->quantity;

            // Create transaction
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . strtoupper(Str::random(8)),
                'tracking_token' => Str::uuid(),
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_channel' => $request->payment_channel,
                'order_type' => 'online',
                'note' => $request->note
            ]);

            // Create transaction item
            $transaction->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'subtotal' => $total
            ]);

            // Create shipping address
            $transaction->shippingAddress()->create([
                'recipient_name' => $request->recipient_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'postal_code' => $request->postal_code,
                'note' => $request->note
            ]);

            // Update stock
            $product->decrement('stock', $request->quantity);

            DB::commit();

            // Redirect to payment page or show success message
            return redirect()->route('order.success', $transaction->tracking_token)
                ->with('message', 'Pesanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function success($trackingToken)
    {
        $transaction = Transaction::where('tracking_token', $trackingToken)
            ->with(['items.product', 'shippingAddress'])
            ->firstOrFail();

        return Inertia::render('Frontend/OrderSuccess', [
            'transaction' => $transaction
        ]);
    }
}
