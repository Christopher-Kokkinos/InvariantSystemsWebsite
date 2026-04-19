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
        Schema::create('intake_records', function (Blueprint $table): void {
            $table->string('intake_id')->primary();
            $table->dateTime('created_at_utc');
            $table->string('created_by');
            $table->string('requester_identity');
            $table->string('requester_contact_channel');
            $table->string('system_class');
            $table->text('diagnostic_objective');
            $table->text('scope_boundary_summary');
            $table->text('evidence_a_summary')->nullable();
            $table->text('evidence_b_summary')->nullable();
            $table->text('evidence_c_summary')->nullable();
            $table->text('evidence_d_summary')->nullable();
            $table->text('constraints_sensitivity_availability')->nullable();
            $table->text('requester_cannot_share')->nullable();
            $table->enum('triage_status', ['Untriaged', 'Accept', 'Conditional Accept', 'Pause', 'Decline']);
            $table->text('triage_rationale')->nullable();
            $table->text('missing_information_notes')->nullable();
            $table->text('exclusion_reason')->nullable();
            $table->dateTime('updated_at_utc');
            $table->string('updated_by');

            $table->index('triage_status');
            $table->index('updated_at_utc');
            $table->index('created_at_utc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intake_records');
    }
};
