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
        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('site');
            $table->string('title');
            $table->enum('severity',['critical','high','medium','low'])->nuulable();
            // $table->text('poc')->nullable();
            // $table->text('step_for_reduce')->nullable();
            $table->text('report')->nullable();
            // $table->integer('count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vulnerabilities');
    }
};
