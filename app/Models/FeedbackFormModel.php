<?php

namespace App\Models;

use CodeIgniter\Model;

class FeedbackFormModel extends Model
{
    protected $table = 'client_satisfaction';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'reference_no',
        'client_type',
        'business_name',
        'government_agency',
        'name',
        'contact_number',
        'email_address',
        'office_visited',
        'date',
        'sex',
        'service_availed',
        'other_service',
        'rating_1',
        'rating_2',
        'rating_3',
        'rating_4',
        'rating_5',
        'rating_6',
        'rating_7',
        'rating_8',
        'complaint',
        'suggestions',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'reference_no' => 'required|is_unique[client_satisfaction.reference_no]',
        'client_type' => 'required',
        'name' => 'required',
        'date' => 'required',
    ];
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