<?php

namespace App\Models;

use CodeIgniter\Model;

class TrainingsModel extends Model
{
    protected $table = 'trainings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [

        'training_code',
        'training_title',
        'training_dates',
        'conducted_by',
        'office',
        'immediate_superior',
        'competencies',
        'status',
        'cancel_reason',
        'canceled_at',
        'reschedule_reason',
        'rescheduled_at',
        'rescheduled_at',
        'superior_notified_at',
        'superior_notify_count',
        'created_at',
        'updated_at'

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
