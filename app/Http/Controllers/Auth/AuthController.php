<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite; // Import Socialite for Facebook login

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->intended('home'); // Redirect to your desired route
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->intended('/'); // Redirect to your desired route
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/'); // Redirect to your desired route after logout
    }

    public function facebookCallback(Request $request)
    {
        $accessToken = $request->input('accessToken');

        try {
            // Retrieve user details from Facebook
            $user = Socialite::driver('facebook')->userFromToken($accessToken);

            // dd([
            //     'id' => $user->getId(),
            //     'name' => $user->getName(),
            //     'email' => $user->getEmail(),
            // ]);

            // Find or create the user in your database
            $existingUser = User::where('facebook_id', $user->getId())->first();

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                
                $newUser = new User();
                $newUser->name = $user->getName();
                $newUser->email = $user->getEmail();
                $newUser->facebook_id = $user->getId();
                $newUser->password = bcrypt('default_password');
                $newUser->save();

                Auth::login($newUser);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Facebook Login Error: ', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Unable to login with Facebook.'], 500);
        }
    }

}


