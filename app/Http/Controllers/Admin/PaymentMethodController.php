<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    /**
     * Get the active institution ID.
     */
    private function getInstitutionId(): ?int
    {
        $institutionId = auth()->user()?->active_institution_id;

        return $institutionId ? (int) $institutionId : null;
    }


    /**
     * Display payment methods for the active institution only.
     */
    public function index()
    {
        $institutionId = $this->getInstitutionId();

        if (!$institutionId) {
            return response()->json([
                'success' => false,
                'message' => 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।',
            ], 403);
        }

        $paymentMethods = PaymentMethod::where(
                'institution_id',
                $institutionId
            )
            ->latest()
            ->get();

        return response()->json($paymentMethods);
    }


    /**
     * Store a payment method for the active institution.
     */
    public function store(Request $request)
    {
        $institutionId = $this->getInstitutionId();

        if (!$institutionId) {
            return response()->json([
                'success' => false,
                'message' => 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।',
            ], 403);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'icon' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'account_type' => [
                    'required',
                    'in:cash,mobile,bank',
                ],

                'account_number' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'mobile1' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'mobile2' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'address' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Payment Method
            |--------------------------------------------------------------------------
            */

            $paymentMethod = PaymentMethod::create([
                'institution_id' => $institutionId,
                'user_id' => auth()->id(),

                'name' => $validated['name'],

                'icon' => $validated['icon']
                    ?? 'fa-credit-card',

                'account_type' => $validated['account_type'],

                'account_number' => $validated['account_number']
                    ?? null,

                'mobile1' => $validated['mobile1']
                    ?? null,

                'mobile2' => $validated['mobile2']
                    ?? null,

                'address' => $validated['address']
                    ?? null,
            ]);

            Log::info('Payment method created successfully.', [
                'institution_id' => $institutionId,
                'payment_method_id' => $paymentMethod->id,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment Method সফলভাবে তৈরি হয়েছে।',
                'data' => $paymentMethod,
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'ফর্মের তথ্য যাচাই করুন।',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {

            Log::error('Payment method creation failed.', [
                'institution_id' => $institutionId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment Method তৈরি করা যায়নি।',
            ], 500);
        }
    }


    /**
     * Update a payment method belonging to the active institution.
     */
    public function update(Request $request, $id)
    {
        $institutionId = $this->getInstitutionId();

        if (!$institutionId) {
            return response()->json([
                'success' => false,
                'message' => 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।',
            ], 403);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'icon' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'account_type' => [
                    'required',
                    'in:cash,mobile,bank',
                ],

                'account_number' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'mobile1' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'mobile2' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'address' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Find Payment Method Within Active Institution
            |--------------------------------------------------------------------------
            */

            $paymentMethod = PaymentMethod::where(
                    'institution_id',
                    $institutionId
                )
                ->find($id);

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment Method পাওয়া যায়নি।',
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Update Payment Method
            |--------------------------------------------------------------------------
            */

            $paymentMethod->update([
                'name' => $validated['name'],

                'icon' => $validated['icon']
                    ?? 'fa-credit-card',

                'account_type' => $validated['account_type'],

                'account_number' => $validated['account_number']
                    ?? null,

                'mobile1' => $validated['mobile1']
                    ?? null,

                'mobile2' => $validated['mobile2']
                    ?? null,

                'address' => $validated['address']
                    ?? null,
            ]);

            Log::info('Payment method updated successfully.', [
                'institution_id' => $institutionId,
                'payment_method_id' => $paymentMethod->id,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment Method সফলভাবে আপডেট হয়েছে।',
                'data' => $paymentMethod->fresh(),
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'ফর্মের তথ্য যাচাই করুন।',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {

            Log::error('Payment method update failed.', [
                'institution_id' => $institutionId,
                'payment_method_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment Method আপডেট করা যায়নি।',
            ], 500);
        }
    }


    /**
     * Delete a payment method belonging to the active institution.
     */
    public function destroy($id)
    {
        $institutionId = $this->getInstitutionId();

        if (!$institutionId) {
            return response()->json([
                'success' => false,
                'message' => 'কোনো প্রতিষ্ঠান নির্বাচন করা হয়নি।',
            ], 403);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Find Payment Method Within Active Institution
            |--------------------------------------------------------------------------
            */

            $paymentMethod = PaymentMethod::where(
                    'institution_id',
                    $institutionId
                )
                ->find($id);

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment Method পাওয়া যায়নি।',
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Payment Method
            |--------------------------------------------------------------------------
            */

            $paymentMethod->delete();

            Log::info('Payment method deleted successfully.', [
                'institution_id' => $institutionId,
                'payment_method_id' => $id,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment Method সফলভাবে মুছে ফেলা হয়েছে।',
            ]);

        } catch (\Throwable $e) {

            Log::error('Payment method deletion failed.', [
                'institution_id' => $institutionId,
                'payment_method_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment Method মুছে ফেলা যায়নি।',
            ], 500);
        }
    }
}