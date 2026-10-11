<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Fund;

class LedgerController extends Controller
{
    /**
     * Display all ledgers
     */
    public function index(Request $request)
    {
        $institutionId = auth()->user()->active_institution_id;

        $type = $request->get('type');

        $query = Ledger::where('institution_id', $institutionId);

        if ($type) {
            $query->where(function ($q) use ($type) {
                $q->where('type', $type)
                  ->orWhere('type', 'both');
            });
        }

        $ledgers = $query
            ->with('fund')
            ->orderBy('name')
            ->get();

        $funds = Fund::where('institution_id', $institutionId)
            ->orderBy('name')
            ->get();

        return view('admin.accounting.ledgers.index', compact(
            'ledgers',
            'funds',
            'type'
        ));
    }

    /**
     * Store new ledger
     */
    public function store(Request $request)
    {
        try {

            $institutionId = auth()->user()->active_institution_id;

            if (!$institutionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'প্রতিষ্ঠান নির্বাচন করা হয়নি।'
                ], 422);
            }

            $request->validate([
                'name'    => 'required|string|max:255',
                'type'    => 'required|in:income,expense,both',
                'fund_id' => 'required|exists:funds,id'
            ]);

            // Make sure selected fund belongs to current institution
            $fund = Fund::where('id', $request->fund_id)
                ->where('institution_id', $institutionId)
                ->first();

            if (!$fund) {
                return response()->json([
                    'success' => false,
                    'message' => 'নির্বাচিত ফান্ড এই প্রতিষ্ঠানের নয়।'
                ], 422);
            }

            $ledger = Ledger::create([
                'institution_id' => $institutionId,
                'user_id'        => auth()->id(),
                'name'           => $request->name,
                'type'           => $request->type,
                'fund_id'        => $request->fund_id,
            ]);

            return response()->json([
                'success' => true,
                'id'      => $ledger->id,
                'name'    => $ledger->name,
                'message' => 'লেজার যোগ করা হয়েছে'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'লেজার যোগ করতে ব্যর্থ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ledgers under a specific fund
     */
    public function fundLedgers(Request $request, $id)
    {
        $institutionId = auth()->user()->active_institution_id;

        $type = $request->type;

        $query = Ledger::where('institution_id', $institutionId)
            ->where('fund_id', $id);

        if ($type) {
            $query->where(function ($q) use ($type) {
                $q->where('type', $type)
                  ->orWhere('type', 'both');
            });
        }

        return $query
            ->orderBy('name')
            ->get();
    }
}