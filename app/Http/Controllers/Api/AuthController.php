<?php

namespace App\Http\Controllers\Api;

// app/Http/Controllers/Auth/AuthController.php

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
        $validateUser = Validator::make($request->all(),[
            'email' => 'required|string|email|unique:users',
            'name' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'contact' => 'required|string',
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }


        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        $client = new Client([
            'contact' => $request->contact,
        ]);
    

        $user->client()->save($client);
       $clientdetails= new  ClientDetails([
            'fname' => '',
            'lname' => '',
            'company_name' => '',
            'country' => '',
            'city' => '',
            'state' => '',
            'zip' => '',
            
        ]);
        $client->clientDetails()->save($clientdetails);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'clientID'=>$client->id,
            'user'=>$user,
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}

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
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'status' => false,
                'message' => 'Email & Password does not match with our record.',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully',
            'token' => $user->createToken("api-token")->plainTextToken,
            'clientID'=>$user->client->id,
            'user'=>$user,
        ], 200);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}


public function logout(Request $request) {

    // Revoke the token that was used to authenticate the current request
    $request->user()->currentAccessToken()->delete();
    //$request->user->tokens()->delete(); // use this to revoke all tokens (logout from all devices)
    return response()->json(null, 200);
}
}
