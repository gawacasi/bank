<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;

class MainController extends Controller
{

    public function index()
    {
        $id = session('user.id');
        $user = User::find($id)->toArray();
        $wallet = Wallet::where('user_id', $id)->first()->toArray();
        $transactions = Transaction::where('sender_wallet_id', $wallet['id'])
            ->orWhere('receiver_wallet_id', $wallet['id'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('site.home', [
            'user' => $user,
            'wallet' => $wallet,
            'transactions' => $transactions,
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

        $receiverWallet = $this->findWalletByEmail($request->destinatario_email, $user->id);

        if (!$receiverWallet) {
            return redirect()
                ->back()
                ->withInput()
                ->with('amountError', 'Receiver wallet not found');
        }

        $wallet->balance -= $amount;
        $wallet->save();

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

    public function revert($transaction_id) 
    {
        $transaction_id = $this->decryptId($transaction_id);

        $transaction = Transaction::find($transaction_id);

        if (!$transaction) {
            return redirect()
                ->route('home')
                ->with('error', 'Transaction not found');
        }

        if ($transaction->type !== 'TRA') {
            return redirect()
                ->route('home')
                ->with('error', 'Only transfer transactions can be reverted');
        }

        $senderWallet = Wallet::where('user_id', $transaction->sender_wallet_id)->first();
        $receiverWallet = Wallet::where('user_id', $transaction->receiver_wallet_id)->first();

        if (!$senderWallet || !$receiverWallet) {
            return redirect()
                ->route('home')
                ->with('error', 'Wallet not found');
        }

        if ($receiverWallet->balance < $transaction->amount) {
            return redirect()
                ->route('home')
                ->with('error', 'Receiver has insufficient balance to revert this transaction');
        }

        $receiverWallet->balance -= $transaction->amount;
        $receiverWallet->save();

        $senderWallet->balance += $transaction->amount;
        $senderWallet->save();
        $transaction->type = 'INA';
        $transaction->save();

        Transaction::create([
            'sender_wallet_id'     => $transaction->receiver_wallet_id,
            'receiver_wallet_id'   => $transaction->sender_wallet_id,
            'type'                 => 'REV',
            'amount'               => $transaction->amount,
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'Transaction reverted successfully');
    }



    public function exportTransfersCsvDownload()
    {
        $userId = session('user.id');
        $wallet = Wallet::where('user_id', $userId)->first();

        if (!$wallet) {
            return redirect()->back()->with('error', 'Wallet not found');
        }

        // Include transfers and deposits initiated by this user's wallet
        $transactions = Transaction::with(['senderWallet.user', 'receiverWallet.user'])
            ->where('sender_wallet_id', $wallet->id)
            ->whereIn('type', ['TRA', 'DEP', 'REV', 'INA'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'transactions_'.$wallet->id.'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $out = fopen('php://output', 'w');
            // BOM for Excel compatibility
            fprintf($out, "%s", chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['id', 'created_at', 'type', 'amount', 'sender_name', 'receiver_name']);

            foreach ($transactions as $t) {
                $senderName = optional(optional($t->senderWallet)->user)->name ?: '';
                $receiverName = optional(optional($t->receiverWallet)->user)->name ?: '';

                fputcsv($out, [
                    $t->id,
                    $t->created_at,
                    $t->type,
                    number_format($t->amount, 2, '.', ''),
                    $senderName,
                    $receiverName,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Development/test helper: generate CSV for given user_id query param
    public function exportCsvDownloadTest(Request $request)
    {
        $userId = $request->query('user_id', session('user.id'));

        $wallet = Wallet::where('user_id', $userId)->first();
        if (!$wallet) {
            return response('Wallet not found for user_id '.$userId, 404);
        }

        $transactions = Transaction::where('sender_wallet_id', $wallet->id)
            ->orWhere('receiver_wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'transactions_'.$wallet->id.'_test.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $out = fopen('php://output', 'w');
            fprintf($out, "%s", chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['id', 'created_at', 'type', 'amount', 'sender_wallet_id', 'receiver_wallet_id']);

            foreach ($transactions as $t) {
                fputcsv($out, [
                    $t->id,
                    $t->created_at,
                    $t->type,
                    number_format($t->amount, 2, '.', ''),
                    $t->sender_wallet_id,
                    $t->receiver_wallet_id,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Public form to create transactions (external to login)
    public function externalTransactionForm()
    {
        return view('external.transactions_form');
    }

    // Handle external transaction submission
    public function externalTransactionStore(Request $request)
    {
        $request->validate([
            'created_at' => ['nullable', 'date'],
            'type' => ['required', 'string', 'max:10'],
            'amount' => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'sender_wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'receiver_wallet_id' => ['required', 'integer', 'exists:wallets,id'],
        ]);

        $amount = (float) str_replace(',', '.', $request->amount);

        $now = now();

        $insertId = DB::table('external_transactions')->insertGetId([
            'csv_created_at' => $request->filled('created_at') ? $request->created_at : null,
            'type' => strtoupper($request->type),
            'amount' => $amount,
            'sender_wallet_id' => $request->sender_wallet_id,
            'receiver_wallet_id' => $request->receiver_wallet_id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json(['success' => true, 'external_transaction_id' => $insertId], 201);
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

    private function findWalletByEmail($email, $sender_id)
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
