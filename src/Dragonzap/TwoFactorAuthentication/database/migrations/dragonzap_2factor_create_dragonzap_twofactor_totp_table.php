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
        Schema::create('dragonzap_twofactor_totp', function (Blueprint $table) {
            $table->id();
            // User id forigen key
            $table->unsignedBigInteger('user_id');
            $table->string('friendly_name', 255)->default('Authenticator');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Secret key
            $table->string('secret_key', 255);

            // Confirmed
            $table->boolean('confirmed')->default(0);

            // Last used
            $table->dateTime('last_used')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dragonzap_twofactor_totp');

    }
};
