<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Http\Responses\CollectionResponse;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\StatusOnlyResponse;
use App\Services\ApiService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(): Responsable
    {
        $leads = $this->apiService->all();

        return new CollectionResponse(LeadResource::collection($leads));
    }

    public function create(Request $request): Responsable
    {
        try {
            $newLead = $this->apiService->create($request);
        } catch (\Throwable $e) {
            return new ErrorResponse($e);
        }

        return new CollectionResponse(LeadResource::collection([$newLead]));
    }

    public function update(Request $request, int $id): Responsable
    {
        try {
            $lead = $this->apiService->update($request, $id);
        } catch (\Throwable $e) {
            return new ErrorResponse($e);
        }

        return new CollectionResponse(LeadResource::collection([$lead]));
    }

    public function delete(int $id): Responsable
    {
        try {
            $lead = $this->apiService->delete($id);
        } catch (\Throwable $e) {
            return new ErrorResponse($e);
        }

        return new StatusOnlyResponse();
    }
}
