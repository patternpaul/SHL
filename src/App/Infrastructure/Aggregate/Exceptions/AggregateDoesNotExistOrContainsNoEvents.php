<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 15-09-07
 * Time: 10:58 AM
 */

namespace App\Infrastructure\Aggregate\Exceptions;

use App\Infrastructure\Aggregate\AggregateException;

class AggregateDoesNotExistOrContainsNoEvents extends AggregateException
{
    /**
     * AggregateDoesNotExistOrContainsNoEvents constructor.
     */
    public function __construct()
    {
        parent::__construct("This Aggregate does not exist or it does not contain any events.");
    }
}
