<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MailingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MailingListController extends Controller
{
    /**
     * Subscribe to mailing list
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:mailing_list,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $subscription = MailingList::create([
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to our mailing list!',
                'data' => [
                    'id' => $subscription->id,
                    'email' => $subscription->email,
                    'created_at' => $subscription->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe. Please try again later.',
            ], 500);
        }
    }
}
