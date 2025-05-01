<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['items.product', 'creator', 'shippingAddress'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('transaction_code', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('order_type'), function ($q) use ($request) {
                $q->where('order_type', $request->order_type);
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            });

        $perPage = $request->input('perPage', 10);

        return Inertia::render('Dashboard/transactions/Index', [
            'transactions' => $query->latest()->paginate($perPage),
            'filters' => $request->only(['search', 'status', 'order_type', 'perPage']),
            'products' => Product::select('id', 'name', 'price', 'stock')->get(),
            'message' => session('message')
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/transactions/Create', [
            'products' => Product::select('id', 'name', 'price', 'stock')->where('is_active', true)->get()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'note' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Calculate total
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $total += $product->price * $item['quantity'];
            }

            // Create transaction
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . strtoupper(Str::random(8)),
                'tracking_token' => Str::uuid(),
                'total' => $total,
                'status' => 'pending',
                'note' => $request->note,
                'order_type' => 'offline',
                'created_by' => Auth::id()
            ]);

            // Create transaction items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Update stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi");
                }
                $product->decrement('stock', $item['quantity']);

                $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $product->price * $item['quantity']
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard.transactions.index')
                ->with('message', 'Transaksi berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($transaction_code)
    {
        $transaction = Transaction::where('transaction_code', $transaction_code)->firstOrFail();

        return Inertia::render('Dashboard/transactions/Edit', [
            'transaction' => $transaction->load(['items.product', 'creator']),
            'products' => Product::select('id', 'name', 'price', 'stock')->where('is_active', true)->get()
        ]);
    }

    public function update(Request $request, $transaction_code)
    {
        try {
            $transaction = Transaction::where('transaction_code', $transaction_code)->firstOrFail();

            $request->validate([
                'status' => 'required|in:pending,paid,processing,shipped,delivered,completed,cancelled,failed',
                'note' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $transaction->update([
                'status' => $request->status,
                'note' => $request->note
            ]);

            if ($request->status === 'cancelled' && $transaction->status !== 'cancelled') {
                // Return stock if transaction is cancelled
                foreach ($transaction->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            DB::commit();

            return redirect()->route('dashboard.transactions.index')
                ->with('message', 'Status transaksi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction update error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($transaction_code)
    {
        try {
            $transaction = Transaction::where('transaction_code', $transaction_code)->firstOrFail();

            if (!in_array($transaction->status, ['cancelled', 'failed'])) {
                throw new \Exception('Hanya transaksi yang dibatalkan atau gagal yang dapat dihapus');
            }

            $transaction->delete();

            return redirect()->route('dashboard.transactions.index')
                ->with('message', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Transaction deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
