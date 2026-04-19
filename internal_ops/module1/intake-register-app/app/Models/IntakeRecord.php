<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeRecord extends Model
{
    use HasFactory;

    protected $table = 'intake_records';

    protected $primaryKey = 'intake_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'intake_id',
        'created_at_utc',
        'created_by',
        'requester_identity',
        'requester_contact_channel',
        'system_class',
        'diagnostic_objective',
        'scope_boundary_summary',
        'evidence_a_summary',
        'evidence_b_summary',
        'evidence_c_summary',
        'evidence_d_summary',
        'constraints_sensitivity_availability',
        'requester_cannot_share',
        'triage_status',
        'triage_rationale',
        'missing_information_notes',
        'exclusion_reason',
        'updated_at_utc',
        'updated_by',
    ];

    protected $casts = [
        'created_at_utc' => 'datetime',
        'updated_at_utc' => 'datetime',
    ];
}
