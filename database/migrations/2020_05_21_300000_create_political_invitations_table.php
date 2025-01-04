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
        Schema::create('political_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('political_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['political_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('political_invitations');
    }
};
