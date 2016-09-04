<?php
namespace App\Http\Controllers;

use App\Infrastructure\Aggregate\IAggregateRepository;

class DumpController extends Controller
{
    private $agg;
    public function __construct(IAggregateRepository $agg)
    {
        $this->agg = $agg;
    }

    public function dump()
    {
        return response()->json($this->agg->getAllDomainEvents());
    }
}
