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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->foreignId('status_id');
            $table->foreignId('area_id');
            $table->foreignId('user_id');
            $table->foreignId('type_activity_id');
            $table->string('title');
            $table->date('today_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('location_id');
            $table->foreignId('office_id');
            $table->foreignId('division_id');
            $table->foreignId('department_id');
            $table->integer('progress');
            $table->string('observation');
            $table->string('search')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
