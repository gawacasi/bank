<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\alert;
use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginSub(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|email',
                'password'  => 'required|min:6|max:12'
            ],
            [
                'email.required' => 'email is Required',
                'email.email' => 'Invalid Email',
                'password.required' => 'Password is Required',
                'password.min' => 'Invalid Password',
                'password.max' => 'Invalid Password',
            ]
            );

        $email = $request->input('email');
        $password = $request->input('password');


        $user = User::where('email', $email)
                    ->first();

        if(!$user){
            return redirect()
                    ->back()
                    ->withInput()
                    ->with('loginError', 'Wrong email/Password combination');
        }
        
        if(!password_verify($password, $user->password)){
            return redirect()
                    ->back()
                    ->withInput()
                    ->with('loginError', 'Wrong email/Password combination');
        }

        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        session([
            'user' =>   [ 
                'id'        => $user->id,
                'email'  => $user->email
            ]  
        ]);

        return redirect()->to('/');
    }

    public function createAccount()
    {
        return view('createAccount');
    }

    public function createAccountSubmit(Request $request)
    {
        $userModel = new User();
        
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password'  => 'required|min:6|max:12',
                'password_confirmation' => 'required|same:password'
            ],
            [
                'name.required' => 'Name is Required',
                'email.required' => 'Email is Required',
                'email.email' => 'Invalid Email',
                'email.unique' => 'Email already in use',
                'password.required' => 'Password is Required',
                'password.min' => 'Password must be at least 6 characters',
                'password.max' => 'Password must not exceed 12 characters',
                'password_confirmation.required' => 'Password Confirmation is Required',
                'password_confirmation.same' => 'Passwords do not match'
            ]
            );
        

        $name = $request->input('name');
        $email = $request->input('email');
        $password = password_hash($request->input('password'), PASSWORD_BCRYPT);
        
        $userModel->name = $name;
        $userModel->email = $email;
        $userModel->password = $password;

        $userModel->save();

        return redirect()->to('/login')->with('accountCreated', 'Account created successfully! You can now log in.');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->to('/login');
    }
}
