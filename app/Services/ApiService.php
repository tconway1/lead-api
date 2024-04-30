<?php

namespace App\Services;

use App\Repositories\Contracts\RepositoryInterface;

class ApiService
{
    protected $leadRepository;
    public function __construct(RepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function all()
    {
        return $this->leadRepository->all()->load('address');
    }
}
