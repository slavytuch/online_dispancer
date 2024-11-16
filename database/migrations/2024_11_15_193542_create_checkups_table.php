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
        Schema::create('checkups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type');
            $table->string('status')->default('not-started');
            $table->json('checkup_data')->nullable();
            $table->dateTime('start_at');
            $table->date('deadline');
            $table->integer('try')->default(0);
            $table->text('description');
            $table->bigInteger('patient_param_id')->nullable();
            $table->bigInteger('patient_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkups');
    }
};
