<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->string('status', 20)->default('open')->index()->after('best_answer_id');
            $table->timestamp('last_activity_at')->nullable()->index()->after('status');
            $table->foreignId('duplicate_of_id')->nullable()->after('last_activity_at')
                ->constrained('questions')->nullOnDelete();
            $table->index(['status', 'created_at']);
        });

        DB::table('questions')->orderBy('id')->each(function (object $question) {
            DB::table('questions')->where('id', $question->id)->update([
                'slug' => Str::slug($question->title).'-'.$question->id,
                'last_activity_at' => $question->updated_at ?? $question->created_at,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['duplicate_of_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropColumn(['slug', 'status', 'last_activity_at', 'duplicate_of_id']);
        });
    }
};
