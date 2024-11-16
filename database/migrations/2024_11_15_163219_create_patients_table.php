<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('active')->default(true);
            $table->string('sex');
            $table->string('name');
            $table->string('last_name');
            $table->string('patronymic')->nullable();
            $table->integer('telegram_id')->nullable();
            $table->string('photo')->nullable();
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->string('dispancer_reason');
            $table->dateTime('dispancer_start');
            $table->dateTime('dispancer_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
