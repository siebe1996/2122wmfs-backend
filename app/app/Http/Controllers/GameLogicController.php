<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameCollection;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameLogicController extends Controller
{
    public function gotKilled($gameId)
    {
        $userId = Auth::id();
        //user die gekilled wordt
        $gameUser = Game::where('id', $gameId)
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })->with(['usersWithPivot' => function ($query) use ($userId) {
                $query->where('users.id', $userId);
            }])->first();
        //user die gekilled wordt zijn target
        $targetIdKilled = $gameUser->usersWithPivot->first()->pivot->target_id;
        $killer = Game::where('id', $gameId)
            ->with(array('usersWithPivot' => function ($query) use ($userId) {
                $query->where('target_id', $userId);
             }))->first();
        $gameUser->usersWithPivot->first()->pivot->update(['target_id' => NULL, 'alive' => false, 'when_killed' => Carbon::now()]);
        $killer->usersWithPivot->first()->pivot->update(['kills' => DB::raw('kills + 1'), 'target_id' => $targetIdKilled]);
        $killerId = $killer->usersWithPivot->first()->id;
        User::where('id', $killerId)->update(['total_kills' => DB::raw('total_kills + 1')]);
        User::where('id', $userId)->update(['deaths' => DB::raw('deaths + 1')]);

        return response(['data' => ['u got killed' ]], 200)
            ->header('Content-Type', 'application/json');
    }
}