<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Ledger,
    SubLedger,
    Fund,
    PaymentMethod,
    Cashier,
    Transaction,
    TransactionItem
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'expense');

        if (!in_array($type, ['income', 'expense'])) {
            $type = 'expense';
        }

        $institutionId = auth()->user()->active_institution_id;

        if (!$institutionId) {
            abort(403, 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Unique Voucher Number
        |--------------------------------------------------------------------------
        */

        $voucherNo = $this->generateUniqueVoucherNumber(
            $type,
            $institutionId
        );

        /*
        |--------------------------------------------------------------------------
        | Ledgers
        |--------------------------------------------------------------------------
        | Income tab  -> income ledgers only
        | Expense tab -> expense ledgers only
        */

        $ledgers = Ledger::where('institution_id', $institutionId)
            ->where('type', $type)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Funds
        |--------------------------------------------------------------------------
        | Current funds table does not have type column.
        | Therefore funds are institution-wise common.
        */

        $funds = Fund::where('institution_id', $institutionId)
            ->where('type', $type)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Payment Methods
        |--------------------------------------------------------------------------
        */

        $paymentMethods = PaymentMethod::all();

        /*
        |--------------------------------------------------------------------------
        | Cashiers
        |--------------------------------------------------------------------------
        */

        $cashiers = Cashier::all();

        /*
        |--------------------------------------------------------------------------
        | Transactions
        |--------------------------------------------------------------------------
        */

        $transactions = Transaction::with([
                'items.ledger',
                'items.subLedger',
                'fund',
                'paymentMethod',
                'cashier'
            ])
            ->where('institution_id', $institutionId)
            ->where('type', $type)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Balance
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
                'cashiers',
                'transactions',
                'balance'
            )
        );
    }


    public function store(Request $request)
    {
        try {

            Log::info('Transaction store started', [
                'request_type' => $request->type,
                'all_data' => $request->all()
            ]);

            DB::beginTransaction();

            $institutionId = auth()->user()->active_institution_id;

            if (!$institutionId) {
                throw new \Exception(
                    'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */

            $validated = $request->validate([
                'type' => 'required|in:income,expense',
                'date' => 'required|date',

                'fund_id' => [
                    'required',
                    'exists:funds,id'
                ],

                'payment_method_id' => [
                    'required',
                    'exists:payment_methods,id'
                ],

                'cashier_id' => [
                    'required',
                    'exists:cashiers,id'
                ],

                'note' => 'nullable|string',
                'items' => 'required|json'
            ]);

            $type = $validated['type'];

            /*
            |--------------------------------------------------------------------------
            | Decode Items
            |--------------------------------------------------------------------------
            */

            $items = json_decode($request->items, true);

            if (!is_array($items) || empty($items)) {
                throw new \Exception(
                    'কোনো আইটেম পাওয়া যায়নি।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Fund
            |--------------------------------------------------------------------------
            */

            $fund = Fund::where('id', $request->fund_id)
                ->where('institution_id', $institutionId)
                ->where('type', $type)
                ->first();

            if (!$fund) {
                throw new \Exception(
                    'নির্বাচিত Fund এই প্রতিষ্ঠানের অন্তর্ভুক্ত নয়।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Payment Method
            |--------------------------------------------------------------------------
            */

            $paymentMethodExists = PaymentMethod::where(
                'id',
                $request->payment_method_id
            )->exists();

            if (!$paymentMethodExists) {
                throw new \Exception(
                    'নির্বাচিত Payment Method পাওয়া যায়নি।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Cashier
            |--------------------------------------------------------------------------
            */

            $cashierExists = Cashier::where(
                'id',
                $request->cashier_id
            )->exists();

            if (!$cashierExists) {
                throw new \Exception(
                    'নির্বাচিত Cashier পাওয়া যায়নি।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Ledger + Sub Ledger
            |--------------------------------------------------------------------------
            */

            foreach ($items as $index => $item) {

                if (empty($item['ledger_id'])) {
                    throw new \Exception(
                        'আইটেম #' .
                        ($index + 1) .
                        '-এ Ledger নির্বাচন করা হয়নি।'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Ledger must belong to current institution
                | and must match transaction type
                |--------------------------------------------------------------------------
                */

                $ledger = Ledger::where('id', $item['ledger_id'])
                    ->where('institution_id', $institutionId)
                    ->where('type', $type)
                    ->first();

                if (!$ledger) {
                    throw new \Exception(
                        'আইটেম #' .
                        ($index + 1) .
                        '-এর Ledger এই Transaction Type-এর জন্য বৈধ নয়।'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Sub Ledger
                |--------------------------------------------------------------------------
                */

                if (!empty($item['sub_ledger_id'])) {

                    $subLedgerExists = SubLedger::where(
                            'id',
                            $item['sub_ledger_id']
                        )
                        ->where('ledger_id', $ledger->id)
                        ->exists();

                    if (!$subLedgerExists) {
                        throw new \Exception(
                            'আইটেম #' .
                            ($index + 1) .
                            '-এর Sub Ledger নির্বাচিত Ledger-এর অন্তর্ভুক্ত নয়।'
                        );
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
                    (float) $item['amount'] <= 0
                ) {
                    throw new \Exception(
                        'আইটেম #' .
                        ($index + 1) .
                        '-এর Amount সঠিক নয়।'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Generate Voucher
            |--------------------------------------------------------------------------
            */

            $voucherNo = $this->generateUniqueVoucherNumber(
                $type,
                $institutionId
            );

            /*
            |--------------------------------------------------------------------------
            | Calculate Total
            |--------------------------------------------------------------------------
            */

            $totalAmount = array_sum(
                array_map(
                    fn ($item) => (float) ($item['amount'] ?? 0),
                    $items
                )
            );

            if ($totalAmount <= 0) {
                throw new \Exception(
                    'Transaction-এর মোট Amount 0 হতে পারে না।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Create Transaction
            |--------------------------------------------------------------------------
            */

            $transaction = Transaction::create([
                'institution_id' => $institutionId,
                'voucher_no' => $voucherNo,
                'type' => $type,
                'fund_id' => $request->fund_id,
                'payment_method_id' => $request->payment_method_id,
                'cashier_id' => $request->cashier_id,
                'total_amount' => $totalAmount,
                'date' => $request->date,
                'note' => $request->note
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Transaction Items
            |--------------------------------------------------------------------------
            */

            foreach ($items as $item) {

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'ledger_id' => $item['ledger_id'],
                    'sub_ledger_id' => $item['sub_ledger_id'] ?? null,
                    'amount' => $item['amount'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            Log::info(
                'Transaction committed successfully',
                [
                    'institution_id' => $institutionId,
                    'type' => $type,
                    'voucher_no' => $voucherNo,
                    'transaction_id' => $transaction->id
                ]
            );

            return redirect()
                ->route(
                    'dashboard.transactions.index',
                    ['type' => $type]
                )
                ->with(
                    'success',
                    "লেনদেন সফলভাবে সংরক্ষণ করা হয়েছে (ভাউচার: {$voucherNo})"
                );

        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            Log::error(
                'Transaction validation error',
                [
                    'errors' => $e->errors()
                ]
            );

            return back()
                ->with(
                    'error',
                    'ভ্যালিডেশন Error: ' .
                    json_encode(
                        $e->errors(),
                        JSON_UNESCAPED_UNICODE
                    )
                )
                ->withInput();

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Transaction store error',
                [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]
            );

            return back()
                ->with(
                    'error',
                    'লেনদেন সংরক্ষণ করতে ব্যর্থ: ' .
                    $e->getMessage()
                )
                ->withInput();
        }
    }


    /**
     * Generate unique voucher number
     */
    private function generateUniqueVoucherNumber(
        $type,
        $institutionId
    ) {
        $lastTransaction = Transaction::where(
                'institution_id',
                $institutionId
            )
            ->where('type', $type)
            ->latest('id')
            ->first();

        if ($lastTransaction) {

            $lastNumber = (int) $lastTransaction->voucher_no;

            $newNumber = $lastNumber + 1;

        } else {

            $newNumber = 1;
        }

        return str_pad(
            $newNumber,
            2,
            '0',
            STR_PAD_LEFT
        );
    }


    public function destroy($id)
    {
        try {

            $institutionId = auth()->user()->active_institution_id;

            if (!$institutionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।'
                ], 403);
            }

            $transaction = Transaction::where(
                    'institution_id',
                    $institutionId
                )
                ->findOrFail($id);

            $type = $transaction->type;

            /*
            |--------------------------------------------------------------------------
            | Delete Items
            |--------------------------------------------------------------------------
            */

            $transaction->items()->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete Transaction
            |--------------------------------------------------------------------------
            */

            $transaction->delete();

            return response()->json([
                'success' => true,
                'type' => $type
            ]);

        } catch (\Exception $e) {

            Log::error(
                'Transaction delete error',
                [
                    'message' => $e->getMessage()
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get payment method balance
     * for current institution only.
     */
    private function getBalance($institutionId): array
    {
        $balance = [];

        $paymentMethods = PaymentMethod::all();

        /*
        |--------------------------------------------------------------------------
        | Cashiers
        |--------------------------------------------------------------------------
        */

        $cashiers = Cashier::all();

        foreach ($paymentMethods as $pm) {

            /*
            |--------------------------------------------------------------------------
            | Income
            |--------------------------------------------------------------------------
            */

            $incomeTotal = Transaction::where(
                    'institution_id',
                    $institutionId
                )
                ->where('payment_method_id', $pm->id)
                ->where('type', 'income')
                ->sum('total_amount');

            /*
            |--------------------------------------------------------------------------
            | Expense
            |--------------------------------------------------------------------------
            */

            $expenseTotal = Transaction::where(
                    'institution_id',
                    $institutionId
                )
                ->where('payment_method_id', $pm->id)
                ->where('type', 'expense')
                ->sum('total_amount');

            $netBalance = $incomeTotal - $expenseTotal;

            $balance[$pm->id] = [
                'name' => $pm->name,
                'icon' => $pm->icon ?? 'fa-credit-card',
                'total' => $netBalance,
                'total_display' => abs($netBalance),
                'is_negative' => $netBalance < 0,
                'income' => $incomeTotal,
                'expense' => $expenseTotal,
                'members' => []
            ];

            /*
            |--------------------------------------------------------------------------
            | Cashier Balance
            |--------------------------------------------------------------------------
            */

            foreach ($cashiers as $cashier) {

                $cashierIncome = Transaction::where(
                        'institution_id',
                        $institutionId
                    )
                    ->where('payment_method_id', $pm->id)
                    ->where('cashier_id', $cashier->id)
                    ->where('type', 'income')
                    ->sum('total_amount');

                $cashierExpense = Transaction::where(
                        'institution_id',
                        $institutionId
                    )
                    ->where('payment_method_id', $pm->id)
                    ->where('cashier_id', $cashier->id)
                    ->where('type', 'expense')
                    ->sum('total_amount');

                $cashierNet = $cashierIncome - $cashierExpense;

                if ($cashierNet != 0) {

                    $balance[$pm->id]['members'][] = [
                        'name' => $cashier->name,
                        'amount' => abs($cashierNet),
                        'is_negative' => $cashierNet < 0
                    ];
                }
            }
        }

        return $balance;
    }
}
