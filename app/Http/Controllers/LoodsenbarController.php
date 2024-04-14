<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\FlunkyDJMusic;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoodsenbarController extends Controller
{

    public function viewHome()
    {
        $user = Auth::user();
        
        return view('speltakken.loodsen.loodsenbar.home');
    }

}