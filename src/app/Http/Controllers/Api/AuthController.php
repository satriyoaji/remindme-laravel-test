<?php

namespace App\Http\Controllers\Api;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\NewAccessToken;

class AuthController extends Controller
{
    /**
     * Refresh token string
     * @param User $user
     * @return NewAccessToken
     */
    protected function createRefreshToken(User $user): NewAccessToken
    {
        $token = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rtExpiration')));

        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => $token->plainTextToken,
            'expires_at' => Carbon::now()->addMinutes(config('sanctum.rtExpiration')),
        ]);

        return $token;
    }

    /**
     * Login The User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]);

            if($validateUser->fails()){
                return response()->json([
                    'ok' => false,
                    'msg' => 'validation error',
                    'err' => $validateUser->errors()
                ], 400);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'ok' => false,
                    'err' => 'ERR_INVALID_CREDS',
                    'msg' => 'incorrect username or password'
                ], 401);
            }

            $user = Auth::user();

            $accessTokenResult = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addSeconds(config('sanctum.expirationSeconds')));
            $refreshToken = $this->createRefreshToken($user);

            return response()->json([
                'ok' => true,
                'data' => [
                    'user' => $user,
                    'access_token' => $accessTokenResult->plainTextToken,
                    'refresh_token' => $refreshToken->plainTextToken
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'msg' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh Token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        try {
            $bearerToken = $request->bearerToken();

            $refreshToken = RefreshToken::where('token', $bearerToken)
                ->where('expires_at', '>', Carbon::now())
                ->first();
            if (!$refreshToken) {
                return response()->json([
                    'ok' => false,
                    'err' => 'ERR_INVALID_REFRESH_TOKEN',
                    'msg' => 'invalid refresh token'
                ], 401);
            }

            $accessToken = $refreshToken->user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addSeconds(config('sanctum.expirationSeconds')));

            return response()->json([
                'ok' => true,
                'data' => [
                    'access_token' => $accessToken->plainTextToken,
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
