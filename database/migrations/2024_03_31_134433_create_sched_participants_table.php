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
        Schema::create('sched_participants', function (Blueprint $table) {
            $table->uuid('id');
            $table->softDeletes();
            $table->string('sched_id');
            $table->string('participant_id');
            $table->decimal('score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sched_participants');
    }
};
