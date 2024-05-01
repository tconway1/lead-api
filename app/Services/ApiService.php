<?php

namespace App\Services;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiService
{
    protected $leadRepository;

    const VALIDATION_RULES = [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'electric_bill' => 'required|integer|max:2147483647',
        'street' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'state' => 'required|string|min:2|max:2',
        'zip_code' => 'required|integer|min_digits:5|max_digits:5'
    ];

    const ADDRESS_FIELDS = [
        'street',
        'city',
        'state',
        'zip_code',
    ];

    public function __construct(RepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function all(): Collection
    {
        return $this->leadRepository->all()->load('address');
    }

    public function create(Request $request): Model
    {
        $request->validate(self::VALIDATION_RULES);

        return $this->leadRepository->create([
            'leadData' => $this->_cleanInput($request->except(self::ADDRESS_FIELDS)),
            'addressData' => $this->_cleanInput($request->only(self::ADDRESS_FIELDS)),
        ]);
    }

    private function _cleanInput(array $input): array
    {
        array_walk($input, function(&$item) {
            $item = !is_numeric($item) ? trim(strip_tags($item)) : $item;
        });

        return $input;
    }
}
