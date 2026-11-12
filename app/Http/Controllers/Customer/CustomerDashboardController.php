<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $devices = Device::all();

        return view('customer.dashboard', compact(
            'devices'
        ));
    }
}