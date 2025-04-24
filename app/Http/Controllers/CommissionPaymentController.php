<?php

namespace App\Http\Controllers;

use App\Models\CommissionPayment;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class CommissionPaymentController extends Controller
{
    /**
     * Display a listing of the commission payments
     */
    public function index(Request $request)
    {
        // Check user permissions
        if (!Gate::allows('view-commissions')) {
            abort(403, 'Unauthorized action.');
        }

        $query = CommissionPayment::with(['user', 'shop']);

        // Filter by vendor/user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by shop
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->input('shop_id'));
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->input('payment_method') !== 'all') {
            $query->where('payment_method', $request->input('payment_method'));
        }

        // Filter by period
        if ($request->has('period_start')) {
            $query->where('paid_at', '>=', $request->input('period_start'));
        }
        if ($request->has('period_end')) {
            $query->where('paid_at', '<=', $request->input('period_end') . ' 23:59:59');
        }

        // Get payments
        $payments = $query->orderBy('paid_at', 'desc')->paginate(15);

        // Get vendors and shops for filters
        $sellers = User::where('role', 'vendeur')->orderBy('name')->get();
        $shops = Gate::allows('admin') 
            ? Shop::orderBy('name')->get() 
            : Auth::user()->shops;

        // Statistics
        $stats = [
            'total_payments' => CommissionPayment::count(),
            'total_amount' => CommissionPayment::sum('amount'),
            'payment_methods' => CommissionPayment::select('payment_method')
                ->distinct()
                ->pluck('payment_method')
                ->toArray(),
        ];

        return view('commission-payments.index', compact('payments', 'sellers', 'shops', 'stats'));
    }

    /**
     * Display the specified commission payment
     */
    public function show(CommissionPayment $payment)
    {
        // Check user permissions
        if (!Gate::allows('view-commissions')) {
            abort(403, 'Unauthorized action.');
        }

        $payment->load(['user', 'shop', 'paidByUser']);

        // Get associated commissions
        $commissions = $payment->commissions;

        return view('commission-payments.show', compact('payment', 'commissions'));
    }

    /**
     * Display payment history for a specific vendor
     */
    public function vendorHistory(User $user)
    {
        // Check if current user can view this vendor's payments
        if (Auth::user()->role === 'vendeur' && Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $payments = CommissionPayment::with(['shop'])
            ->where('user_id', $user->id)
            ->orderBy('paid_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_payments' => $payments->total(),
            'total_amount' => CommissionPayment::where('user_id', $user->id)->sum('amount'),
            'payment_methods' => CommissionPayment::where('user_id', $user->id)
                ->select('payment_method')
                ->distinct()
                ->pluck('payment_method'),
        ];

        return view('commission-payments.vendor-history', compact('user', 'payments', 'stats'));
    }

    /**
     * Display payment history for a specific shop
     */
    public function shopHistory($shopId)
    {
        // Check permissions
        if (!Gate::allows('view-shop-commissions', $shopId)) {
            abort(403, 'Unauthorized action.');
        }

        $shop = Shop::findOrFail($shopId);
        
        $payments = CommissionPayment::with(['user', 'paidByUser'])
            ->where('shop_id', $shopId)
            ->orderBy('paid_at', 'desc')
            ->paginate(15);

        // Payment statistics
        $stats = [
            'total_payments' => $payments->total(),
            'total_amount' => CommissionPayment::where('shop_id', $shopId)->sum('amount'),
            'payment_methods' => CommissionPayment::where('shop_id', $shopId)
                ->select('payment_method')
                ->distinct()
                ->pluck('payment_method'),
        ];

        // Group payments by vendor
        $paymentsByVendor = CommissionPayment::where('shop_id', $shopId)
            ->select('user_id')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total_amount')
            ->groupBy('user_id')
            ->with('user')
            ->get();

        return view('commission-payments.shop-history', compact('shop', 'payments', 'stats', 'paymentsByVendor'));
    }
} 