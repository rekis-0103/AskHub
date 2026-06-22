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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
            $table->timestamp('suspended_at')->nullable()->index()->after('is_admin');
        });

        DB::table('users')->orderBy('id')->each(function (object $user) {
            $base = Str::slug($user->name, '_') ?: 'user';
            DB::table('users')->where('id', $user->id)->update([
                'username' => Str::limit($base, 42, '').'_'.$user->id,
            ]);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->string('category', 30)->default('other')->index()->after('reportable_id');
            $table->foreignId('resolved_by')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();
            $table->text('resolution_note')->nullable()->after('resolved_by');
            $table->unique(
                ['user_id', 'reportable_type', 'reportable_id'],
                'reports_unique_per_user_content'
            );
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 80)->index();
            $table->nullableMorphs('subject');
            $table->text('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 80)->unique();
            $table->text('description');
            $table->string('icon', 10)->default('★');
            $table->string('criteria_type', 30);
            $table->unsignedInteger('criteria_value');
            $table->timestamps();
        });

        Schema::create('badge_user', function (Blueprint $table) {
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('awarded_at');
            $table->primary(['badge_id', 'user_id']);
            $table->index(['user_id', 'awarded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('audit_logs');

        Schema::table('reports', function (Blueprint $table) {
            $table->dropUnique('reports_unique_per_user_content');
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['category', 'resolved_by', 'resolution_note']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'suspended_at']);
        });
    }
};
