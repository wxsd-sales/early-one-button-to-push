<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_email')->index()->nullable();
            $table->string('place_id');
            $table->string('product');
            $table->string('mac');
            $table->string('serial');
            $table->string('primary_sip_url');
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->foreign('user_email')->references('email')->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
