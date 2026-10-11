<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    /**
     * Display all subscription transactions.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Only Super Admin can access transaction history.
        if (!$user || $user->roleSlug !== 'super_admin') {
            abort(403, 'Only Super Admin can access transaction history.');
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction Query
        |--------------------------------------------------------------------------
        */

        $query = SubscriptionPayment::query()
            ->with([
                'institution',
                'subscription.plan',
                'verifier',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('sender_number', 'like', "%{$search}%")
                    ->orWhereHas('institution', function ($institutionQuery) use ($search) {
                        $institutionQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->orWhereHas('subscription.plan', function ($planQuery) use ($search) {
                        $planQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Method Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_method')) {
            $query->where(
                'payment_method',
                $request->input('payment_method')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->input('date_to')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Summary Statistics
        |--------------------------------------------------------------------------
        |
        | Summary cards represent all payment records, not just the filtered
        | results. Revenue includes approved payments only.
        |
        */

        $totalTransactions = SubscriptionPayment::count();

        $totalRevenue = SubscriptionPayment::query()
            ->where('status', 'approved')
            ->sum('amount');

        $pendingPayments = SubscriptionPayment::query()
            ->where('status', 'pending')
            ->count();

        $rejectedPayments = SubscriptionPayment::query()
            ->where('status', 'rejected')
            ->count();

        $approvedTransactions = SubscriptionPayment::query()
            ->where('status', 'approved')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Available Payment Methods
        |--------------------------------------------------------------------------
        */

        $paymentMethods = SubscriptionPayment::query()
            ->whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->distinct()
            ->orderBy('payment_method')
            ->pluck('payment_method');

        /*
        |--------------------------------------------------------------------------
        | Paginated Transactions
        |--------------------------------------------------------------------------
        */

        $transactions = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('super-admin.transactions.index', compact(
            'transactions',
            'totalTransactions',
            'totalRevenue',
            'pendingPayments',
            'rejectedPayments',
            'approvedTransactions',
            'paymentMethods'
        ));
    }

    /**
     * Display a single transaction.
     */
    public function show(Request $request, int $paymentId)
    {
        $user = $request->user();

        // Only Super Admin can view transaction details.
        if (!$user || $user->roleSlug !== 'super_admin') {
            abort(403, 'Only Super Admin can view transaction details.');
        }

        $transaction = SubscriptionPayment::query()
            ->with([
                'institution',
                'subscription.plan',
                'verifier',
            ])
            ->findOrFail($paymentId);

        return view(
            'super-admin.transactions.show',
            compact('transaction')
        );
    }
}