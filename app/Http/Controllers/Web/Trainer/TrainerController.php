<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    public function dashboard()
    {
        $trainer = Auth::guard('trainer_web')->user();
        return view('trainer.dashboard', ['trainer' => $trainer]);
    }
}
