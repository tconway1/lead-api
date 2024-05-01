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

    public function update(array $data, Model $lead): Model
    {
        $lead->update($data);

        return $lead;
    }

    public function delete(Model $lead): bool
    {
        return $lead->delete();
    }

    public function find(int $id): ?Model
    {
        return Lead::find($id);
    }
}
