<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDomainEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aggregate_id');
            $table->integer('aggregate_version');
            $table->text('aggregate_contract');
            $table->text('event_contract');
            $table->unique(['aggregate_id','aggregate_version']);
            $table->index('aggregate_id');
        });

        DB::statement("ALTER TABLE domain_events ADD event_data LONGBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('domain_events');
    }
}
