<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Repositories\LeadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JustSteveKing\StatusCode\Http;

class ApiController extends Controller
{
    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function index(): JsonResponse
    {
        $leads = $this->leadRepository->all();

        return response()->json(
            data: LeadResource::collection($leads),
            status: Http::OK->value
        );
    }
}
