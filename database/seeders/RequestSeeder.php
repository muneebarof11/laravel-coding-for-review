<?php

namespace Database\Seeders;

use App\Http\Controllers\Controller;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allUsers = DB::table('users')->get();
        foreach($allUsers as $user) {
//            echo 'User: ' . $user->name . '-' . $user->email;
//            echo PHP_EOL;
//            echo PHP_EOL;

            // add to connections
            $ids = (new Controller())->getConnectionSuggestions($user);
            $suggestedUsers = DB::table('users')->whereNotIn('id', $ids)->limit(5)->get();
            foreach($suggestedUsers as $su) {
                DB::table('connections')->insert([
                    'user_id' => $user->id,
                    'connection_id' => $su->id
                ]);
            }

            // add to sent request
            $ids = (new Controller())->getConnectionSuggestions($user);
            $suggestedUsers = DB::table('users')->whereNotIn('id', $ids)->limit(5)->get();
            foreach($suggestedUsers as $su) {
//                echo 'Suggested: ';
//                echo $su->name . ' ' . $su->email;
//                echo PHP_EOL;

                DB::table('connection_requests')->insert([
                    'sender_id' => $user->id,
                    'recipient_id' => $su->id
                ]);
            }

//            echo '--------------';
//            echo PHP_EOL;

        }
    }
}
