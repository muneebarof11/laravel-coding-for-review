<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConnectionController extends Controller
{
    /**
     * 1) List of all connected users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dataSet = User::select('connections.id', 'users.name', 'users.email')
            ->join('connections', 'users.id', '=', 'connections.connection_id')
            ->where('connections.user_id', '=',  $request->user()->id)
            ->paginate(10);

        if($dataSet->isEmpty())
        {
            $dataSet = User::select('connections.id', 'users.name', 'users.email')
                ->join('connections', 'users.id', '=', 'connections.user_id')
                ->where('connections.connection_id', '=',  $request->user()->id)
                ->paginate(10);
        }

        return $this->sendResponse($dataSet);
    }

    /**
     * 2) Remove any connection
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $connection = Connection::find($id);
        if (!$connection instanceof Connection)
            return $this->sendResponse([], 'Unable to process your request, please try again!', Response::HTTP_BAD_REQUEST);

        $connection->delete();
        return $this->sendResponse([], 'Connected removed!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $connections = Connection::select('id')
            ->where('user_id', '=', $user->id)
            ->orWhere('connection_id', '=', $user->id)
            ->count();

        $connectionSentRequests = ConnectionRequest::select('id')
            ->where('sender_id', '=', $user->id)
            ->count();

        $connectionReceivedRequests = ConnectionRequest::select('id')
            ->where('recipient_id', '=', $user->id)
            ->count();

        $idsToSkip = $this->getConnectionSuggestions($user);
        $suggestion = User::whereNotIn('id', $idsToSkip)->count();

        $stats = [
            'connections' => $connections,
            'sent' => $connectionSentRequests,
            'received' => $connectionReceivedRequests,
            'suggestion' => $suggestion
        ];

        return $this->sendResponse($stats);
    }

    public function common(Request $request): JsonResponse
    {

    }
}
