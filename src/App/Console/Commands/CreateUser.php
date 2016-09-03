<?php

namespace App\Console\Commands;

use App\Infrastructure\Aggregate\AggregateRepository;
use App\Infrastructure\Aggregate\DomainEvent;
use App\Infrastructure\Logger;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\HandlerStack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Event;
use OpenCloud\Common\Transport\Utils;
use GuzzleHttp\Psr7\Stream;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createuser {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'createuser';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $user = User::create(
            [
                'name' => $email,
                'email' => $email,
                'password' => bcrypt($password),
            ]
        );
        $user->save();
    }
}
