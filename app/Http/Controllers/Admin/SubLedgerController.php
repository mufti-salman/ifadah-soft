<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubLedger;
use App\Models\Ledger;
use Illuminate\Support\Facades\Log;

class SubLedgerController extends Controller
{
    /**
     * Store Sub Ledger
     */
    public function store(Request $request)
    {
        try {

            Log::info('SubLedger store called', [
                'request' => $request->all()
            ]);

            /*
            |--------------------------------------------------------------------------
            | Current Institution
            |--------------------------------------------------------------------------
            */

            $institutionId = auth()->user()->active_institution_id;

            if (!$institutionId) {

                return response()->json([
                    'success' => false,
                    'message' => 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।'
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */

            $request->validate([
                'ledger_id' => 'required|exists:ledgers,id',
                'name' => 'required|string|max:255'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Validate Ledger
            |--------------------------------------------------------------------------
            | Ledger must belong to current institution.
            */

            $ledger = Ledger::where('id', $request->ledger_id)
                ->where('institution_id', $institutionId)
                ->first();

            if (!$ledger) {

                return response()->json([
                    'success' => false,
                    'message' => 'নির্বাচিত Ledger এই প্রতিষ্ঠানের অন্তর্ভুক্ত নয়।'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Create Sub Ledger
            |--------------------------------------------------------------------------
            */

            $subLedger = SubLedger::create([
                'ledger_id' => $ledger->id,
                'institution_id' => $institutionId,
                'name' => $request->name
            ]);

            Log::info('SubLedger created', [
                'id' => $subLedger->id,
                'ledger_id' => $ledger->id,
                'institution_id' => $institutionId
            ]);

            /*
            |--------------------------------------------------------------------------
            | Return JSON
            |--------------------------------------------------------------------------
            | Blade expects:
            | data.success
            | data.id
            | data.name
            */

            return response()->json([
                'success' => true,
                'id' => $subLedger->id,
                'name' => $subLedger->name,
                'ledger_id' => $subLedger->ledger_id,
                'message' => 'Sub Ledger সফলভাবে সংরক্ষণ করা হয়েছে।'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::error(
                'SubLedger validation error',
                [
                    'errors' => $e->errors()
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'তথ্য সঠিকভাবে প্রদান করুন।',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            Log::error(
                'SubLedger store error',
                [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get Sub Ledgers by Ledger
     */
    public function getByLedger($id)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Current Institution
            |--------------------------------------------------------------------------
            */

            $institutionId = auth()->user()->active_institution_id;

            if (!$institutionId) {

                return response()->json([
                    'success' => false,
                    'message' => 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।'
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Ledger
            |--------------------------------------------------------------------------
            */

            $ledger = Ledger::where('id', $id)
                ->where('institution_id', $institutionId)
                ->first();

            if (!$ledger) {

                return response()->json([
                    'success' => false,
                    'message' => 'Ledger পাওয়া যায়নি।'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Get Sub Ledgers
            |--------------------------------------------------------------------------
            */

            $subLedgers = SubLedger::where(
                    'ledger_id',
                    $ledger->id
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'ledger_id'
                ]);

            /*
            |--------------------------------------------------------------------------
            | Return Array
            |--------------------------------------------------------------------------
            | Blade loadSubLedgers() can directly consume this.
            */

            return response()->json($subLedgers);

        } catch (\Exception $e) {

            Log::error(
                'Get SubLedger error',
                [
                    'ledger_id' => $id,
                    'message' => $e->getMessage()
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
