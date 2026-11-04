<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Read-only queries for the TIA dashboard (overview page).
 */
class TiaDashboardModel extends Model
{
    protected $table = 'trainings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /** Rating columns of the self-rater form (Annex D): prefix => number of items. */
    public const SELF_SCORES = ['att' => 5, 'perf' => 5, 'comp' => 4, 'obj' => 4, 'app' => 5];

    /** Rating columns of the superior-rater form (Annex E). */
    public const SUPERIOR_SCORES = ['att' => 5, 'perf' => 5, 'comp' => 5, 'app' => 5];

    /** Every training, newest first. */
    public function trainings(): array
    {
        return $this->db->table('trainings')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();
    }

    /** training_code => number of participants. */
    public function participantCounts(): array
    {
        $rows = $this->db->query(
            'SELECT training_code, COUNT(*) AS n FROM training_participants GROUP BY training_code'
        )->getResultArray();

        return array_column($rows, 'n', 'training_code');
    }

    /** Submitted self-rater forms (Annex D): training_code + all rating columns. */
    public function selfResponses(): array
    {
        return $this->ratings('tia_responses', self::SELF_SCORES);
    }

    /** Submitted superior-rater forms (Annex E): training_code + all rating columns. */
    public function superiorResponses(): array
    {
        return $this->ratings('tia_superior_responses', self::SUPERIOR_SCORES);
    }

    private function ratings(string $table, array $scores): array
    {
        $cols = ['training_code'];
        foreach ($scores as $prefix => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $cols[] = "{$prefix}_{$i}";
            }
        }

        return $this->db->table($table)->select(implode(',', $cols))->get()->getResultArray();
    }
}