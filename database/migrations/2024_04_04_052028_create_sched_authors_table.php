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
        Schema::create('sched_authors', function (Blueprint $table) {
            $table->uuid('id');
            $table->softDeletes();
            $table->boolean('enabled')->default(true);
            $table->string('sched_id');
            $table->string('name');
            $table->string('position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sched_authors');
    }
};
