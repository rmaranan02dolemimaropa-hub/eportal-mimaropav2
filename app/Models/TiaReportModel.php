<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Read-only queries for the TIA reports:
 *  - Annex D-1 (self-rater summary)
 *  - Annex E-1 (superior-rater summary)
 *  - Annex H   (public offerings, 1-5 attendees)
 */
class TiaReportModel extends Model
{
    protected $table = 'tia_responses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // ------------------------------------------------------------------
    // Training pickers
    // ------------------------------------------------------------------

    /** Trainings with their number of self-rater respondents (Annex D-1 and H). */
    public function trainingsWithCounts(): array
    {
        return $this->db->query(
            "SELECT t.training_code, t.training_title, t.training_dates,
                    COUNT(r.id) AS respondents
               FROM trainings t
          LEFT JOIN tia_responses r ON r.training_code = t.training_code
           GROUP BY t.id, t.training_code, t.training_title, t.training_dates, t.created_at
           ORDER BY t.created_at DESC"
        )->getResultArray();
    }

    /** Trainings with their number of superior rating sheets (Annex E-1). */
    public function trainingsWithSuperiorCounts(): array
    {
        return $this->db->query(
            "SELECT t.training_code, t.training_title, t.training_dates,
                    COUNT(r.id) AS respondents
               FROM trainings t
          LEFT JOIN tia_superior_responses r ON r.training_code = t.training_code
           GROUP BY t.id, t.training_code, t.training_title, t.training_dates
           ORDER BY t.id DESC"
        )->getResultArray();
    }

    // ------------------------------------------------------------------
    // Single training
    // ------------------------------------------------------------------

    public function findTraining(string $code): ?array
    {
        return $this->db->table('trainings')
            ->where('training_code', $code)
            ->get()
            ->getRowArray() ?: null;
    }

    /** No. of participants (based on the participants list / attendance sheet). */
    public function participantCount(string $code): int
    {
        return (int) $this->db->table('training_participants')
            ->where('training_code', $code)
            ->countAllResults();
    }

    // ------------------------------------------------------------------
    // Responses
    // ------------------------------------------------------------------

    /** All submitted self-rater responses (Annex D) of a training. */
    public function responses(string $code): array
    {
        return $this->db->table('tia_responses')
            ->where('training_code', $code)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** All submitted superior-rater responses (Annex E) of a training. */
    public function superiorResponses(string $code): array
    {
        return $this->db->table('tia_superior_responses')
            ->where('training_code', $code)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** Headcount of self-rater respondents grouped by 'office' or 'position'. */
    public function countBy(string $code, string $column): array
    {
        // Column names can't be bound as parameters, so whitelist them.
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

    /** Full participants list of a training (attendance sheet). */
    public function participants(string $code): array
    {
        return $this->db->table('training_participants')
            ->where('training_code', $code)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }
}