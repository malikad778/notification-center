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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->nullable(); // Notification class name
            $table->morphs('notifiable'); // User/Team/etc
            $table->string('channel'); // The channel this was sent on
            $table->json('payload');
            $table->string('status')->default('pending'); // Enums will be used in code
            $table->integer('attempts')->default(0);
            $table->string('priority')->default('normal');
            $table->foreignId('notification_group_id')->nullable()->constrained()->nullOnDelete();
            $table->text('error_message')->nullable();
            
            $table->timestamp('read_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
