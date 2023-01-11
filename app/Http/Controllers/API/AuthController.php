<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\SignupRequest;

/**
 * @group Authentication
 *
 * APIs managing authentication
 */
class AuthController extends BaseController
{
    
    public function signIn(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] =  $authUser->createToken('EAuth')->plainTextToken;
            $success['name'] =  $authUser->first_name . ' ' . $authUser->last_name;

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized']);
        }
    }

    
    public function signUp(SignupRequest $request)
    {
        $input = $request->validated();
        $input['password'] = bcrypt($input['password']);
        $input['phone_number'] = $input['phone'];
        unset($input['phone']); 
        $user = User::create($input);
        $success['token'] =  $user->createToken('EAuth')->plainTextToken;
        $success['user'] =  $user;

        return $this->sendResponse($success, 'User created successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->sendResponse([], 'User logged out successfully.');
    }
}
