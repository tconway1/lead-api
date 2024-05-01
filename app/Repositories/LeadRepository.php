<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LeadRepository implements RepositoryInterface
{
    public function all(): Collection
    {
        return Lead::all();
    }

    public function create(array $data): Model
    {
        $lead = Lead::create($data['leadData']);

        $address = $lead->address()->create($data['addressData']);

        return $lead->load('address');
    }

    public function update(array $data, int $id): Model
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
    }

    public function find(int $id): Model
    {
        // TODO: Implement find() method.
    }
}
