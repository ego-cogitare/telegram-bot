<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Stock
 * @package App\Models
 */
class Stock extends Model
{
    /**
     * @var string
     */
    protected $connection = 'arbitrage';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
