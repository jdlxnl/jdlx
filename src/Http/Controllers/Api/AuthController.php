<?php

namespace Jdlx\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jdlx\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function csfr(Request $request)
    {
        return new Response('', 204);
    }

    /**
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        /**
         * @var $user \App\User
         */
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->success(["token" => $user->createToken($request->device_name)->plainTextToken]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function user(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            return response()->success(new UserResource($user));
        }
        return response()->success();

    }

    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->success(new UserResource(Auth::user()));
        } else {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        return response()->success([
            "logged_out" => true,
        ], 200);
    }
}
