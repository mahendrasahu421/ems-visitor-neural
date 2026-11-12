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
        // 1. Add PIN and login tracking fields to employees table
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'login_pin')) {
                $table->string('login_pin', 255)->nullable()->after('employee_password');
            }
            if (!Schema::hasColumn('employees', 'is_pin_enabled')) {
                $table->boolean('is_pin_enabled')->default(false)->after('login_pin');
            }
            if (!Schema::hasColumn('employees', 'pin_set_at')) {
                $table->timestamp('pin_set_at')->nullable()->after('is_pin_enabled');
            }
            if (!Schema::hasColumn('employees', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('pin_set_at');
            }
        });

        // 2. Create employee_otps table for PIN resets and OTP verification
        if (!Schema::hasTable('employee_otps')) {
            Schema::create('employee_otps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->string('identifier', 100)->index(); // mobile or email
                $table->string('otp_code', 10);
                $table->string('purpose', 50)->default('forgot_pin');
                $table->timestamp('expires_at');
                $table->boolean('is_used')->default(false);
                $table->timestamps();

                $table->index(['employee_id', 'purpose', 'is_used']);
                $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            });
        }

        // 3. Create employee_auth_activity_logs table for audit trail
        if (!Schema::hasTable('employee_auth_activity_logs')) {
            Schema::create('employee_auth_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->nullable()->index();
                $table->string('action', 100);
                $table->string('status', 20)->default('success'); // success, failed
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_name', 150)->nullable();
                $table->text('details')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['employee_id', 'action']);
                $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_auth_activity_logs');
        Schema::dropIfExists('employee_otps');

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
            if (Schema::hasColumn('employees', 'pin_set_at')) {
                $table->dropColumn('pin_set_at');
            }
            if (Schema::hasColumn('employees', 'is_pin_enabled')) {
                $table->dropColumn('is_pin_enabled');
            }
            if (Schema::hasColumn('employees', 'login_pin')) {
                $table->dropColumn('login_pin');
            }
        });
    }
};
