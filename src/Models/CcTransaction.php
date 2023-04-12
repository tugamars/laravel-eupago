<?php

namespace CodeTech\EuPago\Models;

use Illuminate\Database\Eloquent\Model;

class CcTransaction extends Model
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'reference',
        'transaction_id',
        'value',
        'state',
    ];


    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scopes a query to only include paid references.
     *
     * @param $query
     * @return mixed
     */
    public function scopePaid($query)
    {
        return $query->where('state', 1);
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the owning mbwayable model.
     */
    public function ccable()
    {
        return $this->morphTo();
    }
}
