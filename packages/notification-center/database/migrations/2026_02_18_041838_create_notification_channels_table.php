<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('driver'); // email | sms | push
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->text('config')->nullable(); // For encrypted array casting
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};
