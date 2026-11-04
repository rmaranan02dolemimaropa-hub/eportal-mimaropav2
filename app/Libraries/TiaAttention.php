<?php

namespace App\Libraries;

use App\Models\TiaDashboardModel;

/**
 * "Needs attention" list for the TIA: trainings where some participants have not
 * submitted Annex D (self-rater) or have not been rated by the superior (Annex E).
 *
 * Same rules as TiaDashboardController::index() with no year/quarter filter:
 *  - canceled trainings are skipped
 *  - a training needs attention when it has participants and any are pending
 *  - sorted by most pending first
 */
class TiaAttention
{
    /**
     * @return array{attention: array, attentionTotal: int}
     */
    public function get(int $limit = 8): array
    {
        $model = new TiaDashboardModel();

        $participants = $model->participantCounts();
        $selfCounts = $this->countByCode($model->selfResponses());
        $supCounts = $this->countByCode($model->superiorResponses());

        $attention = [];

        foreach ($model->trainings() as $t) {
            // Same status handling as the TIA dashboard: only 'canceled' is excluded
            if (strtolower(trim((string) ($t['status'] ?? ''))) === 'canceled') {
                continue;
            }

            $code = $t['training_code'];
            $p = (int) ($participants[$code] ?? 0);
            $self = (int) ($selfCounts[$code] ?? 0);
            $sup = (int) ($supCounts[$code] ?? 0);

            $pendingSelf = max(0, $p - $self);
            $pendingSup = max(0, $p - $sup);

            if ($p > 0 && ($pendingSelf + $pendingSup) > 0) {
                $attention[] = $t + [
                    '_p' => $p,
                    '_self' => $self,
                    '_sup' => $sup,
                    '_pending_self' => $pendingSelf,
                    '_pending_sup' => $pendingSup,
                ];
            }
        }

        usort(
            $attention,
            static fn($a, $b) => ($b['_pending_self'] + $b['_pending_sup']) <=> ($a['_pending_self'] + $a['_pending_sup'])
        );

        return [
            'attention' => array_slice($attention, 0, $limit),
            'attentionTotal' => count($attention),
        ];
    }

    /** [training_code => number of responses] */
    private function countByCode(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $code = $r['training_code'];
            $out[$code] = ($out[$code] ?? 0) + 1;
        }
        return $out;
    }
}