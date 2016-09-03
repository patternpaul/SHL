<?php

namespace App\Infrastructure\Aggregate;

use Illuminate\Database\Eloquent\Model;

class DomainEvent extends Model
{
    protected $table = 'domain_events';
    protected $fillable = [
        'id',
        'aggregate_id',
        'aggregate_version',
        'aggregate_contract',
        'event_data',
        'event_contract'
    ];
}
