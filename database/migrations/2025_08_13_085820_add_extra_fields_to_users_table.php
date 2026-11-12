<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['super-admin','admin','hr','employee'])->default('employee');
            $table->boolean('status')->default(1);
            $table->string('profile_photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type','status','profile_photo','phone','address']);
        });
    }
};
