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
        // 1. FCM Device Tokens Table
        Schema::create('fcm_device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('token');
            $table->string('token_hash', 64)->unique()->comment('SHA-256 hash of token for fast unique lookup');
            $table->enum('device_type', ['android', 'ios', 'web'])->default('android');
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });

        // 2. Push Notifications Log / Inbox Table
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('image_url')->nullable();
            $table->string('action_url')->nullable();
            $table->json('data_payload')->nullable();
            $table->string('sender_type')->default('system'); // system, admin, supervisor
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('target_type')->default('employee'); // all, employee, user, team, custom
            $table->unsignedBigInteger('target_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('sent');
            $table->text('delivery_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'is_read']);
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('fcm_device_tokens');
    }
};
