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
        Schema::create('notification_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_key');  // e.g., 'orders', 'messages'
            $table->string('group_label');
            $table->foreignId('user_id')->constrained();
            $table->integer('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_groups');
    }
};
