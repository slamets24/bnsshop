<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with(['transaction', 'creator'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('tracking_number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('transaction', function ($query) use ($request) {
                        $query->where('transaction_code', 'like', '%' . $request->search . '%');
                    });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            });

        $perPage = $request->input('perPage', 10);

        return Inertia::render('Dashboard/shipments/Index', [
            'shipments' => $query->latest()->paginate($perPage),
            'filters' => $request->only(['search', 'status', 'perPage', 'start_date', 'end_date']),
            'message' => session('message')
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/shipments/Create', [
            'transactions' => Transaction::where('status', 'paid')
                ->whereDoesntHave('shipment')
                ->select('id', 'transaction_code', 'total')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|exists:transactions,id',
                'courier' => 'required|string|max:255',
                'tracking_number' => 'required|string|max:255|unique:shipments',
                'shipping_cost' => 'required|numeric|min:0',
                'estimated_delivery' => 'nullable|date',
                'note' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Create shipment
            $shipment = Shipment::create([
                'transaction_id' => $request->transaction_id,
                'courier' => $request->courier,
                'tracking_number' => $request->tracking_number,
                'shipping_cost' => $request->shipping_cost,
                'estimated_delivery' => $request->estimated_delivery,
                'status' => 'pending',
                'note' => $request->note,
                'created_by' => Auth::id()
            ]);

            // Update transaction status
            $transaction = Transaction::findOrFail($request->transaction_id);
            $transaction->update(['status' => 'shipped']);

            DB::commit();

            return redirect()->route('dashboard.shipments.index')
                ->with('message', 'Pengiriman berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Shipment $shipment)
    {
        return Inertia::render('Dashboard/shipments/Edit', [
            'shipment' => $shipment->load(['transaction', 'creator']),
            'transactions' => Transaction::where('status', 'paid')
                ->whereDoesntHave('shipment')
                ->orWhere('id', $shipment->transaction_id)
                ->select('id', 'transaction_code', 'total')
                ->get()
        ]);
    }

    public function update(Request $request, Shipment $shipment)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|exists:transactions,id',
                'courier' => 'required|string|max:255',
                'tracking_number' => 'required|string|max:255|unique:shipments,tracking_number,' . $shipment->id,
                'shipping_cost' => 'required|numeric|min:0',
                'estimated_delivery' => 'nullable|date',
                'status' => 'required|in:pending,shipped,delivered,cancelled',
                'note' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $oldTransactionId = $shipment->transaction_id;
            $newTransactionId = $request->transaction_id;

            // Update shipment
            $shipment->update([
                'transaction_id' => $request->transaction_id,
                'courier' => $request->courier,
                'tracking_number' => $request->tracking_number,
                'shipping_cost' => $request->shipping_cost,
                'estimated_delivery' => $request->estimated_delivery,
                'status' => $request->status,
                'note' => $request->note
            ]);

            // Update transaction statuses
            if ($oldTransactionId !== $newTransactionId) {
                // Reset old transaction status
                Transaction::where('id', $oldTransactionId)->update(['status' => 'paid']);

                // Update new transaction status
                Transaction::where('id', $newTransactionId)->update(['status' => 'shipped']);
            } else {
                // Update transaction status based on shipment status
                $transactionStatus = $request->status === 'delivered' ? 'delivered' : 'shipped';
                Transaction::where('id', $newTransactionId)->update(['status' => $transactionStatus]);
            }

            DB::commit();

            return redirect()->route('dashboard.shipments.index')
                ->with('message', 'Pengiriman berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment update error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Shipment $shipment)
    {
        try {
            if ($shipment->status !== 'cancelled') {
                throw new \Exception('Hanya pengiriman yang dibatalkan yang dapat dihapus');
            }

            DB::beginTransaction();

            // Reset transaction status
            $shipment->transaction->update(['status' => 'paid']);

            $shipment->delete();

            DB::commit();

            return redirect()->route('dashboard.shipments.index')
                ->with('message', 'Pengiriman berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
