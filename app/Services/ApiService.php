<?php

namespace App\Services;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiService
{
    protected RepositoryInterface $leadRepository;

    const CREATE_RULES = [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'electric_bill' => 'required|integer|max:2147483647',
        'street' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'state' => 'required|string|min:2|max:2',
        'zip_code' => 'required|integer|min_digits:5|max_digits:5'
    ];

    const UPDATE_RULES = [
        'phone' => 'required|integer|min_digits:10|max_digits:10',
    ];

    const QUALITY_RULES = [
        'quality' => 'sometimes|required|string|in:standard,premium'
    ];

    const ADDRESS_FIELDS = [
        'street',
        'city',
        'state',
        'zip_code',
    ];

    const PREMIUM = 'premium';

    const LEAD_NOT_FOUND = 'The associated resource could not be found.';

    public function __construct(RepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function all(Request $request): Collection
    {
        $request->validate(self::QUALITY_RULES);

        // if no quality, return all
        if (empty($request->quality)) {
            return $this->leadRepository->all()->load('address');
        }

        // set operator to be used in query based on quality parameter
        $operator = $request->quality === self::PREMIUM ? '>=' : '<';

        return $this->leadRepository->allWhere([
            'field' => 'electric_bill',
            'operator' => $operator,
            'threshold' => config('leads.threshold')
        ])->load('address');
    }

    public function create(Request $request): Model
    {
        $request->validate(self::CREATE_RULES);

        return $this->leadRepository->create([
            'leadData' => $this->_cleanInput($request->except(self::ADDRESS_FIELDS)),
            'addressData' => $this->_cleanInput($request->only(self::ADDRESS_FIELDS)),
        ]);
    }

    public function update(Request $request, int $id): Model
    {
        $lead = $this->find($id);

        $request->validate(self::UPDATE_RULES);

        return $this->leadRepository->update($request->only('phone'), $lead);
    }

    public function delete(int $id): bool
    {
        $lead = $this->find($id);

        return $this->leadRepository->delete($lead);
    }

    public function find(int $id): Model
    {
        $lead = $this->leadRepository->find($id);

        if (empty($lead)) {
            abort(404, self::LEAD_NOT_FOUND);
        }

        return $lead;
    }

    private function _cleanInput(array $input): array
    {
        array_walk($input, function(&$item) {
            $item = !is_numeric($item) ? trim(strip_tags($item)) : $item;
        });

        return $input;
    }
}
