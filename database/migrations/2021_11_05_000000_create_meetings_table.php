<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('calender_id');
            $table->string('user_email')->index();
            $table->string('subject', 1024);
            $table->timestamp('start');
            $table->timestamp('end');
            $table->string('link',4096);
            $table->json('attendees');
            $table->json('organizer');
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['calender_id', 'user_email']);

            $table->foreign('user_email')->references('email')->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meetings');
    }
}
