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
        'employee_code' => 'required|max_length[50]',
        'name' => 'required|max_length[150]',
        'email' => 'required|valid_email|max_length[150]',
        'position' => 'required|max_length[150]',
    ];
    protected $validationMessages = [];
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

    /**
     * Next employee code in the format EMP-0001, EMP-0002, ...
     */
    public function generateCode(string $prefix = 'EMP-', int $pad = 4): string
    {
        $row = $this->db->query(
            "SELECT MAX(CAST(SUBSTRING(employee_code, ?) AS UNSIGNED)) AS max_no
             FROM {$this->table}
             WHERE employee_code LIKE ?",
            [strlen($prefix) + 1, $prefix . '%']
        )->getRowArray();

        $next = ((int) ($row['max_no'] ?? 0)) + 1;

        return $prefix . str_pad((string) $next, $pad, '0', STR_PAD_LEFT);
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
