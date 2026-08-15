<?php

namespace App\Models;

use CodeIgniter\Model;

class HrRequestModel extends Model
{
    protected $table            = 'hr_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [

        'reference_no',
        'service_availed',
        'service_purpose',
        'fullname',
        'contact_number',
        'email_address',
        'employee_category',
        'period_of_service',
        'position',
        'office_division',
        'certification',
        'ipcr_period',
        'specify_201',
        'contract_of_service_year',
        'other_request',
        'date_requested',
        'status',
        'remarks',
        'date_received',
        'received_by',
        'date_released',
        'released_by',
        'date_processing',
        'date_completed',
        'date_rejected',
        'date_canceled',
        'cancellation_reason'
        

    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
