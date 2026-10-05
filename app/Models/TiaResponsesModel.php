<?php

namespace App\Models;

use CodeIgniter\Model;

class TiaResponsesModel extends Model
{
    protected $table = 'tia_responses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'reference_no',
        'training_code',
        'training_title',
        'training_dates',
        'participant_name',
        'email',
        'position',
        'office',
        'immediate_superior',
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
        'comp_1_text',
        'comp_1',
        'comp_2_text',
        'comp_2',
        'comp_3_text',
        'comp_3',
        'comp_4_text',
        'comp_4',
        'obj_1_text',
        'obj_1',
        'obj_2_text',
        'obj_2',
        'obj_3_text',
        'obj_3',
        'obj_4_text',
        'obj_4',
        'app_1',
        'app_2',
        'app_3',
        'app_4',
        'app_5',
        'other_behavior_changes',
        'measurable_improvements',
        'barriers',
        'barrier_others_text',
        'presented_ideas',
        'ideas_yes',
        'ideas_no',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
}
