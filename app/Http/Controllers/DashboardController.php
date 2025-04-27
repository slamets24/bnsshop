<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current month's data
        $currentMonth = Carbon::now()->month;
        $lastMonth = Carbon::now()->subMonth()->month;
        $currentYear = Carbon::now()->year;

        // Calculate total sales and comparison
        $currentMonthSales = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $lastMonthSales = Transaction::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $salesChange = $lastMonthSales > 0
            ? round((($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : 100;

        // Calculate total orders (online + offline)
        $currentMonthOnlineOrders = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereHas('shippingAddress')
            ->count();

        $currentMonthOfflineOrders = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereDoesntHave('shippingAddress')
            ->count();

        $totalCurrentOrders = $currentMonthOnlineOrders + $currentMonthOfflineOrders;

        $lastMonthOnlineOrders = Transaction::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $currentYear)
            ->whereHas('shippingAddress')
            ->count();

        $lastMonthOfflineOrders = Transaction::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $currentYear)
            ->whereDoesntHave('shippingAddress')
            ->count();

        $totalLastOrders = $lastMonthOnlineOrders + $lastMonthOfflineOrders;

        $ordersChange = $totalLastOrders > 0
            ? round((($totalCurrentOrders - $totalLastOrders) / $totalLastOrders) * 100, 1)
            : 100;

        // Get pending orders
        $pendingOrders = Transaction::where('status', 'pending')->count();
        $lastMonthPendingOrders = Transaction::whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'pending')
            ->count();

        $pendingChange = $lastMonthPendingOrders > 0
            ? round((($pendingOrders - $lastMonthPendingOrders) / $lastMonthPendingOrders) * 100, 1)
            : 100;

        // Calculate conversion rate (completed transactions / total transactions)
        $totalTransactions = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $completedTransactions = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'completed')
            ->count();

        $conversionRate = $totalTransactions > 0
            ? round(($completedTransactions / $totalTransactions) * 100, 1)
            : 0;

        $lastMonthConversion = $this->calculateConversionRate($lastMonth, $currentYear);
        $conversionChange = $lastMonthConversion > 0
            ? round(($conversionRate - $lastMonthConversion), 1)
            : $conversionRate;

        // Get sales data for chart (last 7 days)
        $salesChart = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d M'),
                    'total' => $item->total
                ];
            });

        // Get top selling products
        $topProducts = Product::select('products.*', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', '!=', 'cancelled')
            ->whereMonth('transactions.created_at', $currentMonth)
            ->whereYear('transactions.created_at', $currentYear)
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Get recent activities
        $recentActivities = collect();

        // Add recent transactions
        $recentTransactions = Transaction::with('creator', 'shippingAddress')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                $type = $transaction->shippingAddress ? 'online' : 'offline';
                return [
                    'id' => $transaction->id,
                    'user' => $transaction->creator->name,
                    'action' => 'membuat transaksi ' . $transaction->transaction_code . ' (' . $type . ')',
                    'time' => $transaction->created_at->diffForHumans(),
                    'type' => 'transaction'
                ];
            });
        $recentActivities = $recentActivities->concat($recentTransactions);

        // Add recent shipments
        $recentShipments = Shipment::with('creator')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($shipment) {
                return [
                    'id' => $shipment->id,
                    'user' => $shipment->creator->name,
                    'action' => 'membuat pengiriman ' . $shipment->tracking_number,
                    'time' => $shipment->created_at->diffForHumans(),
                    'type' => 'shipment'
                ];
            });
        $recentActivities = $recentActivities->concat($recentShipments);

        // Sort activities by time
        $recentActivities = $recentActivities->sortByDesc('time')->values()->take(5);

        // Get order status distribution
        $orderStatus = Transaction::select('status', DB::raw('count(*) as total'))
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->total];
            });

        return Inertia::render('Dashboard/Dashboard', [
            'stats' => [
                [
                    'name' => 'Total Penjualan',
                    'value' => 'Rp ' . number_format($currentMonthSales, 0, ',', '.'),
                    'change' => $salesChange . '%',
                    'changeType' => $salesChange >= 0 ? 'positive' : 'negative'
                ],
                [
                    'name' => 'Total Pesanan',
                    'value' => number_format($currentMonthOnlineOrders, 0) . ' Online,' . number_format($currentMonthOfflineOrders, 0) . ' Offline',
                    'change' => $ordersChange . '%',
                    'changeType' => $ordersChange >= 0 ? 'positive' : 'negative'
                ],
                [
                    'name' => 'Pesanan Tertunda',
                    'value' => number_format($pendingOrders, 0),
                    'change' => $pendingChange . '%',
                    'changeType' => $pendingChange <= 0 ? 'positive' : 'negative'
                ],
                [
                    'name' => 'Tingkat Konversi',
                    'value' => $conversionRate . '%',
                    'change' => $conversionChange . '%',
                    'changeType' => $conversionChange >= 0 ? 'positive' : 'negative'
                ]
            ],
            'salesChart' => $salesChart,
            'topProducts' => $topProducts,
            'recentActivity' => $recentActivities,
            'orderStatus' => $orderStatus
        ]);
    }

    private function calculateConversionRate($month, $year)
    {
        $totalTransactions = Transaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $completedTransactions = Transaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->count();

        return $totalTransactions > 0
            ? round(($completedTransactions / $totalTransactions) * 100, 1)
            : 0;
    }
}
