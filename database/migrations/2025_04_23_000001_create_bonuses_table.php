<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('level', ['max', 'medium', 'min'])
          ->default('min')
          ->comment('Уровень бонуса: max (максимальный), medium (средний), min (минимальный)');
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->boolean('is_used')->default(false);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
