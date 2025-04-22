<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmationCodeToUsersTable extends Migration
{
public function up(): void
{
Schema::table('users', function (Blueprint $table) {
$table->string('confirmation_code', 64)
->unique()
->after('remember_token');
});
}

public function down(): void
{
Schema::table('users', function (Blueprint $table) {
$table->dropColumn('confirmation_code');
});
}
}
