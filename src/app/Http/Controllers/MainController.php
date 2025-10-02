<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MainController extends Controller
{

    public function index()
    {
        $id = session('user.id');
        $user = User::find($id)->toArray();
        $wallet = Wallet::where('user_id', $id)->first()->toArray();

        return view('site.home', [
            'user' => $user,
            'wallet' => $wallet,
        ]);
    }

    public function deposit()
    {
        $id =  session('user.id');
        $wallet = Wallet::where('user_id', $id)->first()->toArray();

        return view('site.deposit', [
            'wallet' => $wallet,
        ]);
    }

    public function depositSub(Request $request, $id)
    {
        $id = $this->decryptId($id);

        $request->validate(
            [
                'amount' => [
                    'required',
                    'numeric',
                    'gt:0',
                    'regex:/^\d+(\.\d{1,2})?$/',
                ],
                'password'  => 'required'
            ],
            [
                'amount.required' => 'The amount is required.',
                'amount.numeric'  => 'The amount must be a number.',
                'amount.gt'       => 'The amount must be greater than zero.',
                'amount.regex'    => 'The amount must have at most 2 decimal places.',
                'password.required' => 'Password is Required',
            ]
        );

        $user = User::where('id', $id)
            ->first();

        if (!password_verify($request->password, $user->password)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('passwordError', 'Wrong Password');
        }

        $wallet = Wallet::where('user_id', $id)->first();

        $amount = (float) str_replace(',', '.', $request->amount);

        $wallet->balance += $amount;
        $wallet->save();

        Transaction::create([
            'sender_wallet_id'     => $user->id,
            'receiver_wallet_id'   => $user->id,
            'type'                 => 'DEP',
            'amount'               => $amount,
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Deposit successful');
    }

    public function transfer()
    {
        $id =  session('user.id');
        $wallet = Wallet::where('user_id', $id)->first()->toArray();

        return view('site.transfer', [
            'wallet' => $wallet,
        ]);
    }

    public function transferSub(Request $request, $id)
    {
        $id = $this->decryptId($id);

        $request->validate(
            [
                'amount' => [
                    'required',
                    'numeric',
                    'gt:0',
                    'regex:/^\d+(\.\d{1,2})?$/',
                ],
                'password'  => 'required',
                'destinatario_email'     => 'required|email|exists:users,email'
            ],
            [
                'amount.required' => 'The amount is required.',
                'amount.numeric'  => 'The amount must be a number.',
                'amount.gt'       => 'The amount must be greater than zero.',
                'amount.regex'    => 'The amount must have at most 2 decimal places.',
                'password.required' => 'Password is Required',
                'destinatario_email.required'    => 'Email is Required',
                'destinatario_email.email'       => 'Invalid Email',
                'destinatario_email.exists'      => 'Email does not exist',
            ]
        );

        $user = User::where('id', $id)
            ->first();

        if (!password_verify($request->password, $user->password)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('passwordError', 'Wrong Password');
        }

        $amount = (float) str_replace(',', '.', $request->amount);

        $wallet = Wallet::where('user_id', $id)->first();

        if ($wallet->balance < $amount) {
            return redirect()
                ->back()
                ->withInput()
                ->with('amountError', 'Insufficient balance');
        }

        $wallet->balance -= $amount;
        $wallet->save();
        $receiverWallet = $this->findWalletByEmail($request->destinatario_email, $user->id);
        
        if (!$receiverWallet) {
            return redirect()
                ->back()
                ->withInput()
                ->with('amountError', 'Receiver wallet not found');
        }

        $receiverWallet->balance += $amount;
        $receiverWallet->save();
        
        Transaction::create([
            'sender_wallet_id'     => $user->id,
            'receiver_wallet_id'   => $receiverWallet->user_id,
            'type'                 => 'TRA',
            'amount'               => $amount,
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Deposit successful');
    }

    private function decryptId($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->route('home');
        }

        return $id;
    }

    private function findWalletByEmail($email ,$sender_id)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        $wallet = Wallet::where('user_id', $user->id)
        ->where('user_id', '!=', $sender_id)
        ->first();

        return $wallet;
    }
}
