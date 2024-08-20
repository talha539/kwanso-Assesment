<?php

namespace App\Http\Controllers;

use App\Models\ClientInviteToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        // Apply the Sanctum middleware to sendInvite method in this controller to ensure authenticated requests
        $this->middleware('auth:sanctum')->only('sendInvite');
    }

    /**
     * The signup method allows a new client to register.
     * It validates the input data, creates a new user with a 'pending' status,
     * and returns a message indicating successful registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(Request $request)
    {
        // Validate the input data with custom error messages
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'The email address is already registered.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            // Return validation errors as a JSON response with 422 status code
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Create a new user with 'client' role and 'pending' status
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'status' => 'pending'
        ]);
    
        // Return a success message
        return response()->json(['message' => 'Client registered successfully, awaiting admin invite.'], 201);
    }
    

    /**
     * The login method handles user authentication.
     * It checks if the user's credentials are valid, and if the user is logging in for the first time (status: pending),
     * it validates the invite token before allowing access. Upon successful login, it returns an access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'invite_token' => 'sometimes|string',
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'The password field is required.',
            'invite_token.string' => 'The invite token must be a string.',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Check if the user is logging in for the first time (status is pending)
        if ($user->status === 'pending') {
            // Invite token is mandatory for first-time login
            if (empty($request->invite_token)) {
                return response()->json(['message' => 'Invite token required for first time login'], 403);
            }

            // Validate the invite token
            $tokenRecord = ClientInviteToken::where('user_id', $user->id)
                ->where('token', $request->invite_token)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            // Check if the token is valid and not expired
            if (!$tokenRecord) {
                return response()->json(['message' => 'Invalid or expired invite token'], 403);
            }

            // Token is valid, delete the token record and update user status to active
            $tokenRecord->delete();
            $user->status = 'active';
            $user->save();
        }

        // Create an access token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the access token in the response
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    /**
     * The sendInvite method allows an admin to send an invite token to a client.
     * This token is required for a client's first login. The method checks the user's role,
     * validates the client's email, generates or updates the invite token, and returns the token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvite(Request $request)
    {
        // Ensure that the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the input email
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email'
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.'
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the client user by email
        $user = User::where('email', $request->email)->where(['role'=> 'client'])->first();

        // Check if the client exists
        if (!$user) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        // Check if the client is already active
        if ($user->status == 'active') {
            return response()->json(['message' => 'Client status is already activated'], 404);
        }

        // Generate or update invite token
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addHours(24); // Token valid for 24 hours

        ClientInviteToken::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $token, 'expires_at' => $expiresAt]
        );

        // Return the token and expiration time in the response
        return response()->json([
            'message' => 'Invite token generated successfully',
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }
}
