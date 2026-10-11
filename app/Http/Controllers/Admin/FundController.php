<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Fund;

class FundController extends Controller
{
    /**
     * ============================================================
     * STORE FUND
     * ============================================================
     */
    public function store(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'অনুগ্রহ করে প্রথমে লগইন করুন।',
                ], 401);
            }

            $user = auth()->user();

            /*
            |--------------------------------------------------------------------------
            | Active Institution
            |--------------------------------------------------------------------------
            */

            $institutionId = $user->active_institution_id;

            if (!$institutionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'প্রতিষ্ঠান নির্বাচন করা হয়নি।',
                ], 422);
            }

            $institutionId = (int) $institutionId;

            if ($institutionId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'সঠিক প্রতিষ্ঠান নির্বাচন করা হয়নি।',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Validate
            |--------------------------------------------------------------------------
            */

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'type' => [
                    'required',
                    'in:income,expense',
                ],

                'balance' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],
            ], [

                'name.required' =>
                    'Fund-এর নাম দিন।',

                'name.string' =>
                    'Fund-এর নাম সঠিক নয়।',

                'name.max' =>
                    'Fund-এর নাম সর্বোচ্চ ২৫৫ অক্ষর হতে পারবে।',

                'type.required' =>
                    'Fund-এর ধরন নির্বাচন করা হয়নি।',

                'type.in' =>
                    'Fund-এর ধরন সঠিক নয়।',

                'balance.numeric' =>
                    'Balance অবশ্যই একটি সংখ্যা হতে হবে।',

                'balance.min' =>
                    'Balance ০-এর কম হতে পারবে না।',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Clean Name
            |--------------------------------------------------------------------------
            */

            $name = trim($validated['name']);

            if ($name === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Fund-এর নাম খালি রাখা যাবে না।',
                ], 422);
            }

            $type = $validated['type'];

            /*
            |--------------------------------------------------------------------------
            | Check Duplicate Fund
            |--------------------------------------------------------------------------
            */

            $exists = Fund::query()
                ->where('institution_id', $institutionId)
                ->where('type', $type)
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [mb_strtolower($name)]
                )
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'এই ধরনের একটি Fund একই নামে ইতোমধ্যে রয়েছে।',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Balance
            |--------------------------------------------------------------------------
            */

            $balance = isset($validated['balance'])
                ? (float) $validated['balance']
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Create Fund
            |--------------------------------------------------------------------------
            */

            $fund = DB::transaction(function () use (
                $institutionId,
                $user,
                $name,
                $type,
                $balance
            ) {

                return Fund::create([
                    'institution_id' => $institutionId,
                    'user_id'        => $user->id,
                    'name'           => $name,
                    'type'           => $type,
                    'balance'        => $balance,
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,
                'message' => 'Fund সফলভাবে সংরক্ষণ করা হয়েছে.',

                'fund' => [
                    'id'             => $fund->id,
                    'institution_id' => $fund->institution_id,
                    'user_id'       => $fund->user_id,
                    'name'          => $fund->name,
                    'type'          => $fund->type,
                    'balance'       => $fund->balance,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'তথ্য সঠিকভাবে পূরণ করুন।',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Fund সংরক্ষণ করা যায়নি।',
                'error'   => config('app.debug')
                    ? $e->getMessage()
                    : null,
            ], 500);
        }
    }
}
