<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\SwitchProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Display the specified account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role == 'trader' ? ['user', 'trader'] : [$user->role],
            'is_trader' => $user->is_trader ?? 0,
            'created_at' => $user->created_at,
            'pending_role' => $user->role_pending,
            'trader_status' => $user->is_trader ? '1' : ($user->role_pending == 'trader' ? '0' : null),
            'need_verification' => $user->email_verified_at == null ? true : false,
        ];

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    /**
     * Update the specified account in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()->first()], 422);
        }

        $user = $request->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_trader' => $user->is_trader,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'pending_role' => $user->role_pending,
        ];

        return response()->json(['status' => 'success', 'message' => 'Account updated successfully', 'data' => $data], 200);
    }

    /**
     * Update the switch profile in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switchProfile(Request $request, $type)
    {
        Log::info('switchProfile');
        $user = $request->user();
        $otp = rand(111111, 999999);
        // $user->notify(new SwitchProfile($otp));
        $user->trader_otp = $otp;
        $user->role_pending = $type;
        $user->save();

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_trader' => $user->is_trader,
            'pending_role' => $user->role_pending,
            'created_at' => $user->created_at,
        ];

        if ($user->save()) {
            return response()->json(['status' => 'success', 'message' => 'Profile fetched successfully', 'data' => $data,], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to profile fetched',], 500);
        }
    }

    /**
     * Update the profile to switch in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profileToSwitch(Request $request)
    {
        Log::info('profileToSwitch');
        $user = $request->user();
        $role = $request->input('role');

        if ($role == 'trader') {
            $user->role = 'user';
            if ($user->is_trader == 1) {
                $user->role = 'trader';
            }
        } else {
            $user->role = 'user';
        }

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ];

        Log::info('ProfileToSwitch', $data);

        if ($user->save()) {
            return response()->json(['status' => 'success', 'message' => 'Profile switch successfully', 'data' => $data,], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to profile switch',], 500);
        }
    }

    /**
     * Update the verify profile in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verifyProfile(Request $request)
    {
        $user = $request->user();

        if ($request->input('otp') != $user->trader_otp) {
            return response()->json(['status' => 'error', 'message' => 'Please enter valid OTP.',], 404);
        }

        $user->role = $user->role_pending;

        $user->is_trader = 1;

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_trader' => $user->is_trader,
            'created_at' => $user->created_at,
        ];

        if ($user->save()) {
            return response()->json(['status' => 'success', 'message' => 'Profile fetched successfully', 'data' => $data,], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to profile fetched',], 500);
        }
    }


    /**
     * Update the delete account in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user = $request->user();
        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'Account deleted successfully',], 200);
    }
}
