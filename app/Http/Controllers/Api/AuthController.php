<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetUserEmail;
use App\Notifications\SendOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function forgot(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()->first()], 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            $newPassword = $this->generateAlphanumericOtp(8);
            // $user->notify(new ResetUserEmail($newPassword));
            $user->password = Hash::make($newPassword);
            $user->save();

            return response()->json(['password' => $newPassword, 'message' => 'Your Password has been reset successfully. Please check your email for new password.'], 200);
        } else {
            return response()->json(['message' => 'Email not found'], 404);
        }
    }

    // verify sha password
    private function verifyPassword($password, $storedHash)
    {
        list($algorithm, $iterations, $base64Salt, $base64Hash) = explode(':', $storedHash);

        $salt = base64_decode($base64Salt);
        $storedHash = base64_decode($base64Hash);

        // Hash the input password with the same salt and iterations
        $inputHash = hash_pbkdf2($algorithm, $password, $salt, (int)$iterations, 64, true);

        // Compare the input hash with the stored hash
        return hash_equals($storedHash, $inputHash);
    }

    // generate otp
    private function generateAlphanumericOtp($length = 6)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $otp = '';

        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $otp;
    }

    /**
     * Login the user
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()->first()], 422);
        }

        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $user = Auth::user();
            $token = $user->createToken('CarApp')->plainTextToken;

            if ($user->email_verified_at == null) {
                $otp = rand(111111, 999999);
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
                DB::table('password_reset_tokens')->insert([
                    'email' => $user->email,
                    'token' => $otp,
                    'created_at' => now(),
                ]);

                // $user->notify(new SendOtp($otp));
            }

            $data = [
                'token'     => $token,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'role'      => $user->role,
                'need_verification' => $user->email_verified_at == null ? true : false,
            ];

            return response()->json(['message' => 'Login successful', 'user' => $data], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    // register
    public function register(Request $request)
    {
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');

        // rules
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        try {
            // check if user is exist or not
            $user = User::where('email', $request->input('email'))->first();

            if (!empty($user)) {
                return response()->json(['message' => 'Email already registered, Kindly proceed with other email address.']);
            }

            if ($password == $confirm_password) {
                $otp = rand(111111, 999999);

                $data = DB::transaction(function () use ($request, $password, $otp) {
                    $user = new User();
                    $user->name = $request->input('name');
                    $user->email = $request->input('email');
                    $user->phone = $request->input('phone');
                    $user->role = 'user';
                    $user->password = Hash::make($password);

                    if ($user->save()) {

                        $tokenString = $user->createToken('CarApp')->plainTextToken;

                        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
                        DB::table('password_reset_tokens')->insert([
                            'email' => $user->email,
                            'token' => $otp,
                            'created_at' => now(),
                        ]);

                        // $user->notify(new SendOtp($otp));

                        $data = [
                            'token'     => $tokenString,
                            'name'      => $user->name,
                            'email'     => $user->email,
                            'phone'     => $user->phone,
                            'role'      => $user->role,
                            'need_verification' => $user->email_verified_at == null ? true : false,
                        ];
                        return $data;
                        // return response()->json([ 'message' => 'Registration successful', 'user' => $data, // 'token' => $token, // Uncomment if using tokens ], 200);
                    } else {
                        throw new \Exception('Failed to save user.');
                    }
                });
                return response()->json(['message' => 'Registration successful', 'user' => $data], 200);
            } else {
                return response()->json(['message' => 'Password & Confirm Password should be matched.',], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // verify otp
    public function verifyOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'otp' => 'required',
        ], [
            'otp.required' => 'OTP is required',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()->first()], 422);
        }

        $user = $request->user();

        $otp = DB::table('password_reset_tokens')->where('email', $user->email)->first();

        if ($otp && $otp->token == $request->input('otp')) {
            $user->email_verified_at = now();
            $user->save();

            $data = [
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'role'      => $user->role,
                'need_verification' => $user->email_verified_at == null ? true : false,
            ];
            return response()->json(['message' => 'OTP verified successfully', 'user' => $data], 200);
        } else {
            return response()->json(['message' => 'Invalid OTP' . $user->email,], 400);
        }
    }

    /**
     * Update the logout in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }

    /**
     * Change the authenticated user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        // Validate the incoming request data
        $validate = Validator::make($request->all(), [
            'current_password'     => 'required|string',
            'new_password'         => 'required|string|min:8',
            'confirm_password'     => 'required|string|min:8',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()->first()], 422);
        }

        $user = $request->user();

        if (Hash::check($request->input('current_password'), $user->password)) {
            if ($request->input('new_password') == $request->input('confirm_password')) {
                $user->password = Hash::make($request->input('new_password'));
                $user->save();

                return response()->json(['message' => 'Password changed successfully'], 200);
            } else {
                return response()->json(['message' => 'New password & Confirm password should be matched'], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid current password'], 400);
        }
    }
}
