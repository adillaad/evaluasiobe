<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CPL;
use App\Models\MK;
use App\Models\User;
use App\Models\RPS;
use App\Models\Activity;
use App\Models\CPMK;
use App\Models\CPLMK;


class DosenController extends Controller
{
    public function dashboard(){
        return view('dosen.dashboard');
    }
}
