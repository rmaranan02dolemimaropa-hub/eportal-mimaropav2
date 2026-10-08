<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employees';
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
        'office',
        'division',
        'superior_id',
        'immediate_superior',
        'created_at',
        'updated_at'
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
        'id' => 'permit_empty|is_natural_no_zero',
        'employee_code' => 'required|max_length[50]|is_unique[employees.employee_code,id,{id}]',
        'name' => 'required|max_length[150]',
        'email' => 'required|valid_email|max_length[150]',
        'position' => 'required|max_length[150]',
        'office' => 'permit_empty|max_length[150]',
        'division' => 'permit_empty|max_length[150]',
        'superior_id' => 'permit_empty|is_natural_no_zero|is_not_unique[superiors.id]',
        'immediate_superior' => 'permit_empty|max_length[150]',
    ];
    protected $validationMessages = [
        'employee_code' => [
            'is_unique' => 'That employee code is already used by another employee.',
        ],
        'superior_id' => [
            'is_not_unique' => 'The selected superior does not exist.',
        ],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $builder = $this->where('employee_code', $code);
        if ($exceptId) {
            $builder->where('id !=', $exceptId);
        }
        return $builder->countAllResults() > 0;
    }



    public const OFFICES = [
        'DOLE MIMAROPA REGIONAL OFFICE',
        'DOLE OCCIDENTAL MINDORO PROVINCIAL OFFICE',
        'ORIENTAL MINDORO PROVINCIAL OFFICE',
        'MARINDUQUE PROVINCIAL OFFICE',
        'ROMBLON PROVINCIAL OFFICE',
        'PALAWAN PROVINCIAL OFFICE',
    ];

    /** Returns the official office name (case-insensitive match), or null if empty/not in the list */
    public static function normalizeOffice(?string $value): ?string
    {
        $value = strtoupper(trim((string) $value));
        if ($value === '') {
            return null;
        }
        foreach (self::OFFICES as $office) {
            if (strtoupper($office) === $value) {
                return $office;
            }
        }
        return null;
    }

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