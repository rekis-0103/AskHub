<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('votable'); // question_id or answer_id
            $table->tinyInteger('vote'); // 1 untuk upvote, -1 untuk downvote
            $table->timestamps();
            
            $table->unique(['user_id', 'votable_id', 'votable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};