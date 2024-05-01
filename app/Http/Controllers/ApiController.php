<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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

    public function create(Request $request): JsonResponse
    {
        try {
            $newLead = $this->apiService->create($request);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'code' => $e->status,
                    'title' => Str::headline(class_basename($e)),
                    'detail' => $e instanceof ValidationException ? $e->errors() : $e->getMessage(),
                ],
            ], $e->status);
        }

        return response()->json(
            data: LeadResource::collection([$newLead]),
            status: Http::OK->value
        );
    }
}
