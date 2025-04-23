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
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('vulnerability')->nullable();
            $table->enum('severity',['critical','high','medium','low'])->nuulable();
            $table->string('site');
            $table->integer('count')->default(0);
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
