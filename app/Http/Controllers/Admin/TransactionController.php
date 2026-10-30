<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fund;
use App\Models\Ledger;
use App\Models\PaymentMethod;
use App\Models\SubLedger;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Get the active institution ID.
     */
    private function getInstitutionId(): int
    {
        $institutionId = auth()->user()?->active_institution_id;

        if (!$institutionId) {
            abort(403, 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।');
        }

        return (int) $institutionId;
    }

    /**
     * Display income and expense transactions.
     */
    public function index(Request $request)
    {
        $institutionId = $this->getInstitutionId();

        $type = $request->query('type', 'expense');

        if (!in_array($type, ['income', 'expense'], true)) {
            $type = 'expense';
        }

        /*
        |--------------------------------------------------------------------------
        | Voucher Number
        |--------------------------------------------------------------------------
        */

        $voucherNo = $this->generateUniqueVoucherNumber(
            $type,
            $institutionId
        );

        /*
        |--------------------------------------------------------------------------
        | Institution Ledgers
        |--------------------------------------------------------------------------
        */

        $ledgers = Ledger::where('institution_id', $institutionId)
            ->where('type', $type)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Institution Funds
        |--------------------------------------------------------------------------
        */

        $funds = Fund::where('institution_id', $institutionId)
            ->where('type', $type)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Institution Payment Methods
        |--------------------------------------------------------------------------
        */

        $paymentMethods = PaymentMethod::where(
                'institution_id',
                $institutionId
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Institution Transactions
        |--------------------------------------------------------------------------
        */

        $transactions = Transaction::with([
                'items.ledger',
                'items.subLedger',
                'fund',
                'paymentMethod',
                'creator',
            ])
            ->where('institution_id', $institutionId)
            ->where('type', $type)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Payment Method Balances
        |--------------------------------------------------------------------------
        */

        $balance = $this->getBalance($institutionId);

        return view(
            'admin.transactions.index',
            compact(
                'type',
                'voucherNo',
                'ledgers',
                'funds',
                'paymentMethods',
                'transactions',
                'balance'
            )
        );
    }

    /**
     * Store a new income or expense transaction.
     */
    public function store(Request $request)
    {
        $institutionId = $this->getInstitutionId();

        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'type' => [
                'required',
                'in:income,expense',
            ],

            'date' => [
                'required',
                'date',
            ],

            'fund_id' => [
                'required',
                'integer',
            ],

            'payment_method_id' => [
                'required',
                'integer',
            ],

            'note' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'items' => [
                'required',
                'json',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Decode Items
        |--------------------------------------------------------------------------
        */

        $items = json_decode($validated['items'], true);

        if (!is_array($items) || empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'কমপক্ষে একটি Transaction Item দিতে হবে।',
            ]);
        }

        $type = $validated['type'];

        try {
            /*
            |--------------------------------------------------------------------------
            | Create Transaction and Items Atomically
            |--------------------------------------------------------------------------
            */

            $transaction = DB::transaction(function () use (
                $validated,
                $institutionId,
                $type,
                $items
            ) {
                /*
                |--------------------------------------------------------------------------
                | Validate Fund
                |--------------------------------------------------------------------------
                */

                $fund = Fund::where('id', $validated['fund_id'])
                    ->where('institution_id', $institutionId)
                    ->where('type', $type)
                    ->first();

                if (!$fund) {
                    throw ValidationException::withMessages([
                        'fund_id' => 'নির্বাচিত Fund এই প্রতিষ্ঠানের অথবা এই Transaction Type-এর জন্য বৈধ নয়।',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Payment Method
                |--------------------------------------------------------------------------
                */

                $paymentMethod = PaymentMethod::where(
                        'id',
                        $validated['payment_method_id']
                    )
                    ->where('institution_id', $institutionId)
                    ->first();

                if (!$paymentMethod) {
                    throw ValidationException::withMessages([
                        'payment_method_id' => 'নির্বাচিত Payment Method এই প্রতিষ্ঠানের অন্তর্ভুক্ত নয়।',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Transaction Items
                |--------------------------------------------------------------------------
                */

                $totalAmount = 0;

                foreach ($items as $index => $item) {
                    $itemNumber = $index + 1;

                    if (!is_array($item)) {
                        throw ValidationException::withMessages([
                            'items' => "আইটেম #{$itemNumber}-এর তথ্য সঠিক নয়।",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Ledger ID
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !isset($item['ledger_id']) ||
                        !is_numeric($item['ledger_id']) ||
                        (int) $item['ledger_id'] <= 0
                    ) {
                        throw ValidationException::withMessages([
                            'items' => "আইটেম #{$itemNumber}-এ সঠিক Ledger নির্বাচন করুন।",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Validate Ledger Against Institution and Type
                    |--------------------------------------------------------------------------
                    */

                    $ledger = Ledger::where(
                            'id',
                            (int) $item['ledger_id']
                        )
                        ->where('institution_id', $institutionId)
                        ->where('type', $type)
                        ->first();

                    if (!$ledger) {
                        throw ValidationException::withMessages([
                            'items' => "আইটেম #{$itemNumber}-এর Ledger এই প্রতিষ্ঠানের অথবা Transaction Type-এর জন্য বৈধ নয়।",
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Validate Optional Sub-Ledger
                    |--------------------------------------------------------------------------
                    */

                    $subLedgerId = null;

                    if (
                        isset($item['sub_ledger_id']) &&
                        $item['sub_ledger_id'] !== '' &&
                        $item['sub_ledger_id'] !== null
                    ) {
                        if (
                            !is_numeric($item['sub_ledger_id']) ||
                            (int) $item['sub_ledger_id'] <= 0
                        ) {
                            throw ValidationException::withMessages([
                                'items' => "আইটেম #{$itemNumber}-এর Sub-Ledger ID সঠিক নয়।",
                            ]);
                        }

                        $subLedgerId = (int) $item['sub_ledger_id'];

                        /*
                         * A Sub-Ledger must belong to the selected Ledger.
                         * The selected Ledger has already been validated
                         * against the active institution.
                         */
                        $subLedgerExists = SubLedger::where(
                                'id',
                                $subLedgerId
                            )
                            ->where('ledger_id', $ledger->id)
                            ->exists();

                        if (!$subLedgerExists) {
                            throw ValidationException::withMessages([
                                'items' => "আইটেম #{$itemNumber}-এর Sub-Ledger নির্বাচিত Ledger-এর অন্তর্ভুক্ত নয়।",
                            ]);
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Validate Amount
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !isset($item['amount']) ||
                        !is_numeric($item['amount']) ||
                        !is_finite((float) $item['amount']) ||
                        (float) $item['amount'] <= 0
                    ) {
                        throw ValidationException::withMessages([
                            'items' => "আইটেম #{$itemNumber}-এর Amount অবশ্যই শূন্যের বেশি হতে হবে।",
                        ]);
                    }

                    $amount = round((float) $item['amount'], 2);

                    /*
                    |--------------------------------------------------------------------------
                    | Validate Optional Description
                    |--------------------------------------------------------------------------
                    */

                    if (
                        isset($item['description']) &&
                        !is_string($item['description'])
                    ) {
                        throw ValidationException::withMessages([
                            'items' => "আইটেম #{$itemNumber}-এর Description সঠিক নয়।",
                        ]);
                    }

                    $totalAmount += $amount;
                }

                $totalAmount = round($totalAmount, 2);

                if (
                    !is_finite($totalAmount) ||
                    $totalAmount <= 0
                ) {
                    throw ValidationException::withMessages([
                        'items' => 'Transaction-এর মোট Amount সঠিক নয়।',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Generate Voucher Number
                |--------------------------------------------------------------------------
                */

                $voucherNo = $this->generateUniqueVoucherNumber(
                    $type,
                    $institutionId
                );

                /*
                |--------------------------------------------------------------------------
                | Create Transaction
                |--------------------------------------------------------------------------
                */

                $transaction = Transaction::create([
                    'institution_id' => $institutionId,

                    // Automatically record the authenticated user.
                    'created_by' => auth()->id(),

                    'voucher_no' => $voucherNo,
                    'type' => $type,
                    'fund_id' => $fund->id,
                    'payment_method_id' => $paymentMethod->id,
                    'total_amount' => $totalAmount,
                    'date' => $validated['date'],
                    'note' => $validated['note'] ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Create Transaction Items
                |--------------------------------------------------------------------------
                */

                foreach ($items as $item) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'ledger_id' => (int) $item['ledger_id'],

                        'sub_ledger_id' => (
                            isset($item['sub_ledger_id']) &&
                            $item['sub_ledger_id'] !== '' &&
                            $item['sub_ledger_id'] !== null
                        )
                            ? (int) $item['sub_ledger_id']
                            : null,

                        'amount' => round((float) $item['amount'], 2),

                        'description' => $item['description'] ?? null,
                    ]);
                }

                return $transaction;
            });

            /*
            |--------------------------------------------------------------------------
            | Success Log
            |--------------------------------------------------------------------------
            */

            Log::info('Transaction saved successfully', [
                'institution_id' => $institutionId,
                'transaction_id' => $transaction->id,
                'created_by' => auth()->id(),
                'type' => $type,
                'voucher_no' => $transaction->voucher_no,
                'total_amount' => $transaction->total_amount,
            ]);

            return redirect()
                ->route(
                    'dashboard.transactions.index',
                    ['type' => $type]
                )
                ->with(
                    'success',
                    "লেনদেন সফলভাবে সংরক্ষণ করা হয়েছে। ভাউচার: {$transaction->voucher_no}"
                );

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Throwable $e) {
            Log::error('Transaction store error', [
                'institution_id' => $institutionId,
                'user_id' => auth()->id(),
                'type' => $type,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->with(
                    'error',
                    'লেনদেন সংরক্ষণ করতে ব্যর্থ হয়েছে। অনুগ্রহ করে তথ্য যাচাই করে আবার চেষ্টা করুন।'
                )
                ->withInput();
        }
    }

    /**
     * Generate the next voucher number for an institution and type.
     *
     * This preserves the existing numeric voucher format.
     */
    private function generateUniqueVoucherNumber(
        string $type,
        int $institutionId
    ): string {
        $lastVoucherNumber = Transaction::where(
                'institution_id',
                $institutionId
            )
            ->where('type', $type)
            ->get(['voucher_no'])
            ->map(function ($transaction) {
                return (int) $transaction->voucher_no;
            })
            ->max();

        $newNumber = ((int) $lastVoucherNumber) + 1;

        return str_pad(
            (string) $newNumber,
            2,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Delete a transaction belonging to the active institution.
     */
    public function destroy($id)
    {
        $institutionId = $this->getInstitutionId();

        try {
            DB::transaction(function () use (
                $id,
                $institutionId
            ) {
                $transaction = Transaction::where(
                        'institution_id',
                        $institutionId
                    )
                    ->lockForUpdate()
                    ->findOrFail($id);

                /*
                |--------------------------------------------------------------------------
                | Delete Related Items
                |--------------------------------------------------------------------------
                */

                $transaction->items()->delete();

                /*
                |--------------------------------------------------------------------------
                | Delete Transaction
                |--------------------------------------------------------------------------
                */

                $transaction->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'লেনদেন সফলভাবে মুছে ফেলা হয়েছে।',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'লেনদেনটি পাওয়া যায়নি অথবা এই প্রতিষ্ঠানের অন্তর্ভুক্ত নয়।',
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Transaction delete error', [
                'institution_id' => $institutionId,
                'transaction_id' => $id,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'লেনদেন মুছে ফেলা সম্ভব হয়নি।',
            ], 500);
        }
    }

    /**
     * Calculate income, expense and net balance per payment method.
     */
    private function getBalance(int $institutionId): array
    {
        $balance = [];

        $paymentMethods = PaymentMethod::where(
                'institution_id',
                $institutionId
            )
            ->orderBy('name')
            ->get();

        foreach ($paymentMethods as $paymentMethod) {
            $incomeTotal = Transaction::where(
                    'institution_id',
                    $institutionId
                )
                ->where(
                    'payment_method_id',
                    $paymentMethod->id
                )
                ->where('type', 'income')
                ->sum('total_amount');

            $expenseTotal = Transaction::where(
                    'institution_id',
                    $institutionId
                )
                ->where(
                    'payment_method_id',
                    $paymentMethod->id
                )
                ->where('type', 'expense')
                ->sum('total_amount');

            $netBalance = (float) $incomeTotal - (float) $expenseTotal;

            $balance[$paymentMethod->id] = [
                'name' => $paymentMethod->name,
                'icon' => $paymentMethod->icon ?? 'fa-credit-card',
                'total' => $netBalance,
                'total_display' => abs($netBalance),
                'is_negative' => $netBalance < 0,
                'income' => (float) $incomeTotal,
                'expense' => (float) $expenseTotal,
            ];
        }

        return $balance;
    }
}
