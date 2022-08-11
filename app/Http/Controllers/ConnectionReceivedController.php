<?php

namespace App\Http\Controllers;

use App\Models\ConnectionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConnectionReceivedController extends Controller
{
    /**
     * 1) All received connections request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dataSet = User::select('connection_requests.id', 'users.name', 'users.email')
            ->join('connection_requests', 'users.id', '=', 'connection_requests.sender_id')
            ->where('connection_requests.recipient_id', '=',  $request->user()->id)
            ->paginate(10);

        return $this->sendResponse($dataSet);
    }

    /**
     * 2) Decline received connection request
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $connectionRequest = ConnectionRequest::find($id);
        if(!$connectionRequest instanceof ConnectionRequest)
            return $this->sendResponse([], 'Unable to process your request, please try again!', Response::HTTP_BAD_REQUEST);

        $connectionRequest->delete();
        return $this->sendResponse([], 'Connected request declined!');
    }
}
