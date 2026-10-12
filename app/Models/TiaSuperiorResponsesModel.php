<?php

namespace App\Models;

use CodeIgniter\Model;

class TiaSuperiorResponsesModel extends Model
{
    protected $table = 'tia_superior_responses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'reference_no',
        'training_code',
        'training_title',
        'training_dates',
        'participant_id',
        'participant_name',
        'participant_position',
        'participant_email',
        'superior_name',
        'superior_position',
        'att_1',
        'att_2',
        'att_3',
        'att_4',
        'att_5',
        'perf_1',
        'perf_2',
        'perf_3',
        'perf_4',
        'perf_5',
        'comp_1',
        'comp_1_text',
        'comp_2',
        'comp_2_text',
        'comp_3',
        'comp_3_text',
        'comp_4',
        'comp_4_text',
        'comp_5',
        'comp_5_text',
        'app_1',
        'app_2',
        'app_3',
        'app_4',
        'app_5',
        'other_behavior_changes',
        'measurable_improvements',
        'deferment_reason',
        'reasons',
        'reason_others_text',
    ];
}