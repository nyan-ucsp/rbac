<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @return [string] message
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required|string',
            'role_id' => 'required|unsignedBigInteger'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        if ($request->type != '1') {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'type' => $request->type
            ]);
            $user->save();
            return response()->json([
                'message' => 'Successfully created user!'
            ], 201);
        } else {
            return response()->json(['message' => "Something was worng"], 400);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] username
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function systemLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $credentials = request(['user_name', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeek(2);
        $token->save();
        return response()->json([
            'info' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'new_password' => 'required|string',
            'revoke_all' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        if (Hash::check($request->password, $request->user()->password)) {
            if ($request->revoke_all) {
                $userTokens = $request->user()->tokens;
                foreach ($userTokens as $token) {
                    if ($token->revoked == false) {
                        $token->revoke();
                    }
                }
            }
            $request->user()->fill([
                'password' => bcrypt($request->new_password),
            ])->save();
            if ($request->revoke_all) {
                $user = $request->user();
                $tokenResult = $user->createToken('Access Token');
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addWeek(2);
                $token->save();
                return response()->json([
                    'message' => 'success',
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ], 200);
            } else {
                return response()->json([
                    'message' => 'success'
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }

    /**
     * Add more time to access token
     *
     * @return [string] message
     */
    public function addExpiredTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        if (Hash::check($request->password, $request->user()->password)) {
            $new_expired_time = $request->user()->token()->expires_at->addWeek(2);
            $request->user()->token()->fill([
                'expires_at' => $new_expired_time,
            ])->save();
            return response()->json([
                'message' => 'Successfully added expired time',
                'expires_at' => Carbon::parse($new_expired_time)->toDateTimeString()
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthorize'
            ], 401);
        }
    }
}
