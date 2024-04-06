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
        Schema::create('rubrick_evaluations', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->string('sched_id');
            $table->string('auth_id');
            $table->string('participant_id');
            $table->unsignedBigInteger('insight_id');
            $table->string('eval_score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubrick_evaluations');
    }
};
