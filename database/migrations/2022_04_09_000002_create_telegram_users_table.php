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
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->string('userId');
            $table->string('first_name')->nullable();;
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('customUsername')->nullable();
            $table->string('requestName')->nullable();
            $table->boolean('is_bot');
            $table->boolean('isBanned')->default(false);
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
        Schema::dropIfExists('telegram_users');
    }
};
