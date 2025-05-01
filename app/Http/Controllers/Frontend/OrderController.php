<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Http\Requests\OrderStoreRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    public function store(OrderStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            // Get product
            $product = Product::findOrFail($request->product_id);

            // Check stock
            if ($product->stock < $request->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => ['Stok produk tidak mencukupi']
                ]);
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
                'note' => $request->note,
                'created_by' => null
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
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors());
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
