<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * HELPER METHOD to generate response with custom http code
     *
     * @param $data mixed
     * @param $message string
     * @param $code int
     * @return JsonResponse
     */
    protected function sendResponse(mixed $data = [], string $message = "", int $code = Response::HTTP_OK): JsonResponse
    {
        return (new ApiResource($data))->additional([
            'message' => $message,
            'code' => $code
        ])->response()->setStatusCode($code);
    }

    /**
     * HELPER METHOD to generate error response (500 http code)
     *
     * @param $message string
     * @return JsonResponse
     */
    protected function sendErrorResponse(string $message = ""): JsonResponse
    {
        return (new ApiResource([]))->additional([
            'message' => $message,
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ])->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param User
     * @return array
     */
    public function getConnectionSuggestions(mixed $user): array
    {
        $connections = Connection::select('connection_id')
            ->where('user_id', '=', $user->id)
            ->pluck('connection_id')
            ->toArray();

        if(empty($connections)) {
            $connections = Connection::select('user_id')
                ->where('connection_id', '=', $user->id)
                ->pluck('user_id')
                ->toArray();
        }

        $connectionSentRequests = ConnectionRequest::select('recipient_id')
            ->where('sender_id', '=', $user->id)
            ->pluck('recipient_id')
            ->toArray();

        $connectionReceivedRequests = ConnectionRequest::select('sender_id')
            ->where('recipient_id', '=', $user->id)
            ->pluck('sender_id')
            ->toArray();

        return array_unique(array_merge($connections, $connectionSentRequests, $connectionReceivedRequests, [$user->id]));
    }

}
