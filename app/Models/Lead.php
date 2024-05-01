<?php

namespace App\Models;

use App\Models\Traits\SetNull;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes, SetNull;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected array $notNullable = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Lead $lead) {
            // null the lead fields
            $lead->setFieldsToNull();

            // null the address fields
            $lead->address->setFieldsToNull();

            // delete the address
            $lead->address->delete();
        });
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }
}
