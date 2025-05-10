<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Get pending online transactions that don't have shipments yet
        $pendingTransactions = Transaction::where('status', 'pending')
            ->where('order_type', 'online')
            ->whereDoesntHave('shipment')
            ->with('creator')
            ->latest()
            ->get();

        return Inertia::render('Dashboard/shipments/Index', [
            'shipments' => $query->latest()->paginate($perPage),
            'filters' => $request->only(['search', 'status', 'perPage', 'start_date', 'end_date']),
            'message' => session('message'),
            'pendingTransactions' => $pendingTransactions
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
                'note' => 'nullable|string|max:1000',
            ], [
                'transaction_id.required' => 'Transaksi harus dipilih',
                'transaction_id.exists' => 'Transaksi yang dipilih tidak valid',
                'courier.required' => 'Nama kurir harus diisi',
                'courier.max' => 'Nama kurir maksimal 255 karakter',
                'tracking_number.required' => 'Nomor resi harus diisi',
                'tracking_number.unique' => 'Nomor resi sudah digunakan',
                'tracking_number.max' => 'Nomor resi maksimal 255 karakter',
                'shipping_cost.required' => 'Biaya pengiriman harus diisi',
                'shipping_cost.numeric' => 'Biaya pengiriman harus berupa angka',
                'shipping_cost.min' => 'Biaya pengiriman tidak boleh negatif',
                'estimated_delivery.date' => 'Format tanggal estimasi pengiriman tidak valid',
                'note.max' => 'Catatan maksimal 1000 karakter',
            ]);

            DB::beginTransaction();

            // Get the transaction
            $transaction = Transaction::findOrFail($request->transaction_id);

            // Update transaction status from 'pending' to 'paid' if needed
            if ($transaction->status === 'pending') {
                $transaction->status = 'paid';
                $transaction->save();
            }

            // Create shipment - status akan otomatis diatur ke 'processing' oleh default value di database
            $shipment = Shipment::create([
                'transaction_id' => $request->transaction_id,
                'courier' => $request->courier,
                'tracking_number' => $request->tracking_number,
                'shipping_cost' => $request->shipping_cost,
                'estimated_delivery' => $request->estimated_delivery,
                'status' => 'processing',
                'shipped_at' => null,
                'delivered_at' => null,
                'note' => $request->note,
                'created_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('dashboard.shipments.index')
                ->with('message', 'Pengiriman berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment creation error: ' . $e->getMessage());

            // Deteksi SQL error khusus
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, "shipped_at") !== false) {
                return back()->withErrors([
                    'error' => 'Terjadi kesalahan saat menyimpan data pengiriman. Pastikan semua data terisi dengan benar.'
                ]);
            }

            // Kembalikan pesan error asli jika tidak ada penanganan khusus
            return back()->withErrors(['error' => $errorMessage]);
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
                'tracking_number' => 'required|string|max:255|unique:shipments,tracking_number,' . $shipment->id,
                'courier' => 'required|string|max:255',
                'shipping_cost' => 'required|numeric|min:0',
                'estimated_delivery' => 'nullable|date',
                'status' => 'required|in:processing,shipped,delivered,failed',
                'note' => 'nullable|string|max:1000',
            ], [
                'courier.required' => 'Nama kurir harus diisi',
                'courier.max' => 'Nama kurir maksimal 255 karakter',
                'tracking_number.required' => 'Nomor resi harus diisi',
                'tracking_number.unique' => 'Nomor resi sudah digunakan',
                'tracking_number.max' => 'Nomor resi maksimal 255 karakter',
                'shipping_cost.required' => 'Biaya pengiriman harus diisi',
                'shipping_cost.numeric' => 'Biaya pengiriman harus berupa angka',
                'shipping_cost.min' => 'Biaya pengiriman tidak boleh negatif',
                'status.required' => 'Status pengiriman harus dipilih',
                'status.in' => 'Status pengiriman tidak valid',
                'estimated_delivery.date' => 'Format tanggal estimasi pengiriman tidak valid',
                'note.max' => 'Catatan maksimal 1000 karakter',
            ]);

            DB::beginTransaction();

            // Update shipment
            // Model Shipment akan otomatis memperbarui transaction.status berdasarkan perubahan status
            $shipment->update([
                'courier' => $request->courier,
                'tracking_number' => $request->tracking_number,
                'shipping_cost' => $request->shipping_cost,
                'estimated_delivery' => $request->estimated_delivery,
                'status' => $request->status,
                'note' => $request->note
            ]);

            DB::commit();

            return redirect()->route('dashboard.shipments.index')
                ->with('message', 'Pengiriman berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment update error: ' . $e->getMessage());

            // Deteksi SQL error khusus
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, "shipped_at") !== false) {
                return back()->withErrors([
                    'error' => 'Terjadi kesalahan saat memperbarui data pengiriman. Pastikan semua data terisi dengan benar.'
                ]);
            }

            // Kembalikan pesan error asli jika tidak ada penanganan khusus
            return back()->withErrors(['error' => $errorMessage]);
        }
    }

    public function destroy(Shipment $shipment)
    {
        try {
            if ($shipment->status !== 'failed') {
                throw new \Exception('Hanya pengiriman yang gagal yang dapat dihapus');
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

    // Method untuk membuat shipment dari transaksi yang menunggu
    public function createFromPending($transaction_id)
    {
        // Cari transaksi berdasarkan ID
        $transaction = Transaction::findOrFail($transaction_id);

        return Inertia::render('Dashboard/shipments/Create', [
            'selectedTransaction' => $transaction->only(['id', 'transaction_code', 'total']),
            'transactions' => Transaction::where(function ($query) use ($transaction) {
                $query->where('status', 'paid')
                    ->whereDoesntHave('shipment')
                    ->orWhere('id', $transaction->id);
            })
                ->select('id', 'transaction_code', 'total')
                ->get()
        ]);
    }
}
