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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('id_number')->unique();
            $table->string('email')->unique();
            $table->string('contact')->nullable();
            $table->string('grade_level')->nullable();
            $table->string('gender')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->timestamp('suspend_start_date')->nullable();
            $table->timestamp('suspend_end_date')->nullable();
            $table->boolean('is_suspended')->default(false);
     
            $table->timestamp('email_verified_at')->nullable();
            $table->string('image')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
