<?php

namespace App\Models;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use CodeIgniter\Model;

/** Read-only queries for the TIA Report (Annex D-1). */
class TiaReportModel extends Model
{
    protected $table = 'tia_responses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /** Trainings with how many respondents each has. */
    public function trainingsWithCounts(): array
    {
        return $this->db->query(
            "SELECT t.training_code, t.training_title, t.training_dates, COUNT(r.id) AS respondents
               FROM trainings t
          LEFT JOIN tia_responses r ON r.training_code = t.training_code
           GROUP BY t.training_code, t.training_title, t.training_dates, t.created_at
           ORDER BY t.created_at DESC"
        )->getResultArray();
    }

    public function findTraining(string $code): ?array
    {
        return $this->db->table('trainings')
            ->where('training_code', $code)
            ->get()->getRowArray() ?: null;
    }

    /** No. of participants (based on the participants list). */
    public function participantCount(string $code): int
    {
        return (int) $this->db->table('training_participants')
            ->where('training_code', $code)
            ->countAllResults();
    }

    /** All submitted self-rater responses of a training. */
    public function responses(string $code): array
    {
        return $this->db->table('tia_responses')
            ->where('training_code', $code)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
    }

    /** Headcount grouped by 'office' or 'position'. */
    public function countBy(string $code, string $column): array
    {
        if (!in_array($column, ['office', 'position'], true)) {
            return [];
        }

        return $this->db->query(
            "SELECT {$column} AS label, COUNT(*) AS total
               FROM tia_responses
              WHERE training_code = ?
           GROUP BY {$column}
           ORDER BY total DESC, label ASC",
            [$code]
        )->getResultArray();
    }
}