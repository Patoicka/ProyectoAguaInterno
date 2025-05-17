<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use App\Models\IncidentType;
use App\Models\Incident;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        return Inertia::render("Dashboard", [
            'users' => User::count(),
            'IndicentType'=>IncidentType::count(),
            'Incident'=>Incident::count(),
            'data' => null,
        ]);
    }
}
