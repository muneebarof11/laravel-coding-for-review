<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConnectionSuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $idsToSkip = $this->getConnectionSuggestions($request->user());
        $users = User::whereNotIn('id', $idsToSkip)->paginate(10);

        return $this->sendResponse($users);

    }
}
