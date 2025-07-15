<?php

namespace App\Http\Controllers;

use App\Models\PointHistory;
use App\Models\Task;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Http\Request;

class UserPointController extends Controller
{
    public function index(Request $request)
    {
        $query = UserPoint::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        $userPoints = $query->get();
        $users = User::all();
        $months = UserPoint::select('month')->distinct()->orderBy('month', 'desc')->pluck('month');

        return view('points.index', compact('userPoints', 'users', 'months'));
    }


    public function show($id)
    {
        $userPoint = UserPoint::with(['user', 'histories.task'])->findOrFail($id);
        return view('points.show', compact('userPoint'));
    }
}
