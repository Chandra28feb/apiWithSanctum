<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function addWalletMoney(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'wallet' => ['required', 'numeric', 'max:100', 'min:3','regex:/^\d+(\.\d{1,2})?$/']
            ]);
            if ($validator->fails()) {
                return response(['error' => $validator->errors(),'success' =>false],401);
            }
            $user = User::find(auth()->id());
            $user->increment('wallet',$request->wallet);
            return response()->json([
                'message' => 'Amount added successfully',
                'user' => new UserResource($user),
                'success' =>true
            ]);
        } 
        catch (Throwable $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => __('server error'),
                'success' =>false
            ], 500);
        }
    }

    public function buyCookies(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'cookies_count' => ['required', 'numeric', 'min:1']
            ]);
            if ($validator->fails()) {
                return response(['error' => $validator->errors(),'success' =>false],401);
            }
            $user = User::find(auth()->id());
            $cookies_unit_price = config('global.cookies_unit_price');
            $cookies_count = $request->cookies_count;
            $total = $cookies_count * $cookies_unit_price;
            $available_wallet_balance = auth()->user()->wallet;
            if($available_wallet_balance >= $total) {
                $user->decrement('wallet',$total);
                return response()->json([
                    'message' => 'Cookies purchases successfully',
                    'user' => new UserResource($user),
                    'success' =>true
                ]);
            }
            else {
                return response()->json([
                    'message' => 'Insufficient wallet amount',
                    'total'=>$total,
                    'wallet_amount' => auth()->user()->wallet,
                    'success' =>false
                ], 401);
            }

        } 
        catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => __('server error'),
                'success' =>false
            ], 500);
        }
    }
}
