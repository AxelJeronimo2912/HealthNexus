<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog; // <-- ¡Agrega esta línea!
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index()
    {
        // Aquí ya funcionará porque la clase está importada
        $logs = LoginLog::latest()->paginate(15);

        return view('admin.logs.index', compact('logs'));
    }
}