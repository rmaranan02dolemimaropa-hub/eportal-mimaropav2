<?php

namespace App\Models;

use CodeIgniter\Model;

class SuperiorModel extends Model
{
    protected $table = 'superiors';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'employee_code',
        'name',
        'email',
        'position',
        'immediate_superior',
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
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'employee_code' => 'required|max_length[50]|is_unique[superiors.employee_code,id,{id}]',
        'name' => 'required|max_length[150]|is_unique[superiors.name,id,{id}]',
        'email' => 'permit_empty|valid_email|max_length[150]',
        'position' => 'permit_empty|max_length[150]',
        'immediate_superior' => 'permit_empty|max_length[150]',
        'created_at' => 'permit_empty|valid_date',
        'updated_at' => 'permit_empty|valid_date',
    ];
    protected $validationMessages = [
        'employee_code' => [
            'required' => 'Employee code is required.',
            'is_unique' => 'That employee code is already used by another superior.',
        ],
        'name' => [
            'required' => 'Superior name is required.',
            'is_unique' => 'A superior with that name already exists.',
        ],
        'email' => [
            'valid_email' => 'Please enter a valid email address.',
        ],
    ];


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
