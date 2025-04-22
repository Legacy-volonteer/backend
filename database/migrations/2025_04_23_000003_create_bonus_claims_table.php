<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonus_id')->unique()->constrained('bonuses')->cascadeOnDelete();
            $table->foreignId('volunteer_recipient_id')->constrained('volunteer_recipients')->cascadeOnDelete();
            $table->timestamp('claimed_at')->useCurrent();
            $table->string('issued_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_claims');
    }
};
