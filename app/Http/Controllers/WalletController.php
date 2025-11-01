<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Inertia\Inertia;

class WalletController extends Controller{
    public function index(){
        $wallet = Wallet::with('transactions')->get();
        return response()->json($wallet);
    }
}
