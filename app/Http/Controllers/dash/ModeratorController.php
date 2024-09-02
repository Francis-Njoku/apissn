<?php

namespace App\Http\Controllers\dash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function check_user()
    {
        $user_type = DB::table('users')->where('id', auth()->user()->id)->first();
        abort_if($user_type->role_id != '1', 404);
    }
}
