<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController extends Controller
{

    public function me(Request $request)
    {
        $user = $request->user();
        return $user;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase() // Requires at least one uppercase and one lowercase letter
                    ->symbols(),  // Requires at least one special symbol
            ],
            'dob' => 'required|date', // Date of birth is required and must be a valid date
            'gender' => 'required|in:male,female,other', // Must be one of the allowed values
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], 400)
            );
        }

        $fields = $validator->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('users', $fileName, 'public');
            $fields['image_url'] = asset('storage/' . $filePath); // Store the image URL in the users table
        }

        $user = User::create($fields);

        $user->refresh();

        return successResponse("User registered successfully.");
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return errorResponse("Provided credentials are incorrect.", [], 401);
        }
        $token = $user->createToken($user->name);
        $data = [
            'user' => $user,
            "token" => $token->plainTextToken,
        ];
        return successResponse("Logged in.", $data);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return successResponse("Logged out.");
    }


    public function updateUserImage(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image_url) {
                $relativePath = str_replace(asset('storage/'), '', $user->image_url);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }

            // Store the new image
            $file = $request->file('image');
            $fileName = 'user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('users', $fileName, 'public');

            // Update the user's image URL
            $user->update([
                'image_url' => asset('storage/' . $filePath)
            ]);

            return successResponse("User image updated successfully.", ['image_url' => $user->image_url]);
        }

        return errorResponse("No image file provided.", [], 400);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->symbols(), // Requires at least one special character
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return errorResponse("Current password is incorrect.", [], 400);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return successResponse("Password updated successfully.");
    }
}
