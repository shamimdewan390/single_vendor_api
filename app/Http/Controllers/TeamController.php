<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller
{
    public function index()
    {
        return Team::with('users')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'roles' => 'array',
            'roles.*' => 'string',
        ]);

        $team = Team::create(['name' => $request->name]);

        if ($request->has('user_ids')) {
            foreach ($request->user_ids as $index => $userId) {
                $team->users()->attach($userId, ['role' => $request->roles[$index] ?? 'member']);
            }
        }

        return response()->json($team->load('users'), 201);
    }

    public function show(Team $team)
    {
        return $team->load('users');
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'roles' => 'array',
            'roles.*' => 'string',
        ]);

        if ($request->has('name')) {
            $team->update(['name' => $request->name]);
        }

        if ($request->has('user_ids')) {
            $team->users()->sync([]);
            foreach ($request->user_ids as $index => $userId) {
                $team->users()->attach($userId, ['role' => $request->roles[$index] ?? 'member']);
            }
        }

        return response()->json($team->load('users'), 200);
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(null, 204);
    }
}
