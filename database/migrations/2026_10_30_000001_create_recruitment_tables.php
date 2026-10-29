<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('discord_webhook_url')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sector_id')->references('id')->on('sectors')->nullOnDelete();
        });

        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('location')->nullable();
            $table->string('contract_type')->nullable();
            $table->unsignedInteger('positions_count')->default(1);
            $table->string('status', 20)->default('draft')->index();
            $table->dateTime('opens_at')->nullable();
            $table->dateTime('closes_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('motivation');
            $table->text('experience');
            $table->text('availability');
            $table->string('status', 30)->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('validated_at')->nullable();
            $table->timestamps();
        });

        // Une seule candidature « active » par (candidat, offre). Syntaxe compatible PostgreSQL et SQLite.
        DB::statement(
            "CREATE UNIQUE INDEX applications_active_unique ON applications (user_id, job_post_id) WHERE status NOT IN ('withdrawn', 'rejected', 'not_retained')"
        );

        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('location_or_link')->nullable();
            $table->longText('report')->nullable();
            $table->dateTime('report_finalized_at')->nullable();
            $table->unsignedBigInteger('confirmed_slot_id')->nullable();
            $table->timestamps();
        });

        Schema::create('interview_participants', function (Blueprint $table) {
            $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['interview_id', 'user_id']);
        });

        Schema::create('interview_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proposed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('proposed_by_side', 20);
            $table->dateTime('starts_at');
            $table->unsignedInteger('duration_minutes')->default(30);
            $table->string('status', 20)->default('pending')->index();
            $table->text('refusal_reason')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('responded_at')->nullable();
            $table->timestamps();
        });

        // Un seul créneau en attente par entretien.
        DB::statement(
            "CREATE UNIQUE INDEX interview_slots_one_pending ON interview_slots (interview_id) WHERE status = 'pending'"
        );

        Schema::table('interviews', function (Blueprint $table) {
            $table->foreign('confirmed_slot_id')->references('id')->on('interview_slots')->nullOnDelete();
        });

        Schema::create('opinions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('value', 20);
            $table->text('comment');
            $table->timestamps();
            $table->unique(['application_id', 'user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opinions');
        Schema::table('interviews', fn (Blueprint $table) => $table->dropForeign(['confirmed_slot_id']));
        Schema::dropIfExists('interview_slots');
        Schema::dropIfExists('interview_participants');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('job_posts');
        Schema::table('users', fn (Blueprint $table) => $table->dropForeign(['sector_id']));
        Schema::dropIfExists('sectors');
    }
};
