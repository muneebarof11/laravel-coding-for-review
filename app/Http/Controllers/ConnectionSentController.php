<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ConnectionSentController extends Controller
{
    /**
     * 1) All sent connection requests
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dataSet = User::select('connection_requests.id', 'users.name', 'users.email')
            ->join('connection_requests', 'users.id', '=', 'connection_requests.recipient_id')
            ->where('connection_requests.sender_id', '=',  $request->user()->id)
            ->paginate(10);

        return $this->sendResponse($dataSet);
    }

    /**
     * 2) Send a new connection request to other user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $recipient_email = $request->input('recipient');
            $recipient = User::where('email', $recipient_email)->first();

            /**
             * DUE TO TIME SHORTAGE, I'M PUTTING DATA VALIDATION RULES HERE IN CONTROLLER
             * but AS PER RULE, CONTROLLER SHOULD ONLY HANDLE THE FLOW, NOT VALIDATIONS
             */
            if ($user->email === $recipient_email)
                return $this->sendResponse([], "You cannot sent request to yourself!", Response::HTTP_BAD_REQUEST);

            if (!$recipient instanceof User)
                return $this->sendResponse([], "You are sending connection request to invalid user!", Response::HTTP_BAD_REQUEST);

            $connection_request = ConnectionRequest::where('recipient_id', $recipient->id)
                ->where('sender_id', $user->id)
                ->first();

            if ($connection_request instanceof ConnectionRequest)
                return $this->sendResponse([], "You have already sent request to ${recipient_email}", Response::HTTP_CONFLICT);

            $connection = Connection::where('connection_id', $recipient->id)
                ->where('user_id', $user->id)
                ->first();

            if ($connection instanceof Connection)
                return $this->sendResponse([], "You have already connected with ${recipient_email}", Response::HTTP_CONFLICT);

            $sentRequests = ConnectionRequest::create([
                'sender_id' => $user->id,
                'recipient_id' => $recipient->id
            ]);

            return $this->sendResponse($sentRequests->toArray(), "Your request to sent to ${recipient_email}", Response::HTTP_CREATED);


        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * 3) Accept connection request (received from others)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $sentRequest = ConnectionRequest::find($id);
        if(!$sentRequest instanceof ConnectionRequest)
            return $this->sendResponse([], 'Unable to process your request, please try again!', Response::HTTP_BAD_REQUEST);

        DB::beginTransaction();
        $connection = Connection::create([
            'user_id' => $sentRequest->sender_id,
            'connection_id' => $sentRequest->recipient_id
        ]);
        $sentRequest->delete();
        DB::commit();

        return $this->sendResponse($connection->toArray(), 'Connected request accepted!');
    }

    /**
     * 4) Delete sent request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            /**
             * DUE TO TIME SHORTAGE, I'M PUTTING VALIDATION RULES HERE IN CONTROLLER
             * but AS PER RULE, CONTROLLER SHOULD ONLY HANDLE THE FLOW, NOT VALIDATIONS
             */

            $user = $request->user();

            $connectionRequest = ConnectionRequest::find($id);
            if (!$connectionRequest instanceof ConnectionRequest)
                return $this->sendResponse([], "Unable to cancel the request, please contact system admin!", Response::HTTP_BAD_REQUEST);

            $connectionRequest->delete();
            return $this->sendResponse([], "Request cancelled successfully!");

        } catch (Exception $e) {
            return $this->sendErrorResponse($e->getMessage());
        }
    }
}
