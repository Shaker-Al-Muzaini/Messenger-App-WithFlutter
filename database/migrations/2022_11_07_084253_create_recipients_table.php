<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('recipients', static function (Blueprint $table) {
            $table->primary(['message_id','user_id']);
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('message_id')
                ->constrained('messages')
                ->cascadeOnDelete();
            $table->timestamp('read_at')
                ->nullable();
            $table->softDeletes();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('recipients');
    }
};
