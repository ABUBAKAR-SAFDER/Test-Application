<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function createAdmin()
    {
        // Role::create(['name' => 'User']);

        // $user = User::create([
        //     'first_name' => 'Super',
        //     'last_name' => 'Admin',
        //     'username' => 'superadmin',
        //     'email' => 'superadmin@admin.com',
        //     'password' => Hash::make('superadmin')
        // ]);
        // $user->assignRole('Super Admin');

        return response()->json(['response' => ['status' => true, 'message' => 'Created.']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_username' => 'required',
            'password' => 'required'
        ], [
            'email_username.required' => 'The email/username field is required.'
        ]);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        $user = User::where('username', $request->email_username)
            ->orWhere('email', $request->email_username)
            ->first();

        if (empty($user) || !Hash::check($request->password, $user->password))
            return response()->json(['response' => ['status' => false, 'message' => 'The provided credentials are incorrect.']]);

        $user->role = $user->roles[0]->name;

        // if (in_array($user->role, ['Super Admin']) && !empty($user->tokens->first()))
        //     return response()->json(['response' => ['status' => false, 'message' => 'Your account is currently logged in to another device.']]);

        $user->tokens()->delete();
        $user->token = $user->createToken($user->role)->plainTextToken;

        unset($user->roles);
        unset($user->tokens);

        return response()->json([
            'response' => ['status' => true, 'message' => 'LoggedIn successfully.'],
            'result' => [
                'status' => 200,
                'user' => $user
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();
        return response()->json(['response' => ['status' => true, 'message' => 'Logged out successfully.']]);
    }
}
