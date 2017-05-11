<?php
namespace App\Http\Controllers;

use App\Infrastructure\Aggregate\IAggregateRepository;
use Illuminate\Support\Facades\Input;

class DumpController extends Controller
{
    private $agg;
    public function __construct(IAggregateRepository $agg)
    {
        $this->agg = $agg;
    }

    public function dump()
    {
        return response()->json($this->agg->getAll());
    }

    public function list()
    {
        $count = Input::get('count');

        if (is_null($count)) {
            $count = 10;
        }


        $events = $this->agg->getAllDomainEvents();
        dd(array_slice(array_reverse($events), 0, $count));
    }
}
