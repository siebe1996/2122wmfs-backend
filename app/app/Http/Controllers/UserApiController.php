<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*Gate::authorize('show-all-users');
        $users = User::query();
        $users = $users->paginate(15)->withQueryString();
        $users = new UserCollection($users);
        return response(['data' => $users], 200)
            ->header('Content-Type', 'application/json');*/
        if($request->filled('gameId')){
            $gameId = $request->gameId;
            $userId = Auth::id();
            $game = Game::findOrFail($gameId);
            $users = $game->users;
            $users = new UserCollection($users);

            return response(['data' => $users], 200)
                ->header('Content-Type', 'application/json');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * 'first_name' => 'Dries',
        'last_name' => 'Loco',
        'email' => 'driesloco@odisee.be',
        'password' => Hash::make('Azerty123'),
        'total_kills' => 0,
        'deaths' => 1,
        'games_played' => 1,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
         */

        Gate::authorize('store-user');
        $comment = new User;
        $comment->first_name = "nel";
        $comment->last_name= "li";
        $comment->email = "nelli@odisee.be";
        $comment->password = Hash::make('Azerty123');
        $comment->total_kills = 0;
        $comment->deaths = 1;
        $comment->games_played = 1;
        /*'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')*/
        $comment->save();
        $comment = new UserResource($comment);
        return response(['data' => $comment], 201)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Gate::authorize('show-user', $id);
        $user = new UserResource(User::with('roles')->findOrFail($id));
        return response(['data' => $user], 200)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
