<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JustSteveKing\StatusCode\Http;

class ApiController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(): JsonResponse
    {
        $leads = $this->apiService->all();

        return response()->json(
            data: LeadResource::collection($leads),
            status: Http::OK->value
        );
    }
}
