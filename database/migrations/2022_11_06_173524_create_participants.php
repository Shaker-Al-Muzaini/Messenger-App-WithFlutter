<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('participants', static function (Blueprint $table) {
            $table->primary(['conversation_id','user_id']);
            $table->foreignId('conversation_id')
            ->constrained('conversations')
            ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->enum('role',['admin','member'])->default('member');
            $table->timestamp('joined_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
