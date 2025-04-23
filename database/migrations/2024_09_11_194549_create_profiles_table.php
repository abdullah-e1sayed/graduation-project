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
        Schema::create('profiles', function (Blueprint $table) {

            $table->foreignID('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender',['male','female'])->nullable();            
            $table->char('country',2)->nullable();
            $table->char('locale',2)->default('en');
            $table->uuid('api_token');
            $table->timestamps();
            $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
