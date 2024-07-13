<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function listUsers()
    {
        $users = User::role(['User'])->get();

        return response()->json([
            'response' => ['status' => true, 'message' => 'Users List.'],
            'result' => [
                'status' => 200,
                'users' => $users
            ]
        ]);
    }

    public function createOrUpdateUser(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => ['required', Rule::unique('users')->ignore($request->id)],
            'email' => ['required', Rule::unique('users')->ignore($request->id)]
        ];

        if (empty($request->id)) {
            $rules['password'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        if (empty($request->id))
            $User = new User();
        else
            $User = User::find($request->id);

        $User->first_name = $request->first_name;
        $User->last_name = $request->last_name;
        $User->username = $request->username;
        $User->email = $request->email;
        if (!empty($request->password))
            $User->password = Hash::make($request->password);
        $User->save();

        if (empty($request->id)) {
            $User->assignRole('User');
        }

        $msg = empty($request->id) ? 'User has been created successfully.' : 'User has been updated successfully.';

        return response()->json([
            'response' => ['status' => true, 'message' => $msg]
        ]);
    }

    public function deleteUser(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        User::where('id', $request->id)->delete();

        return response()->json([
            'response' => ['status' => true, 'message' => 'User has been deleted successfully.']
        ]);
    }
}
