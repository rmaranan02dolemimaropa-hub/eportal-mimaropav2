<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TiaDashboardModel;

/**
 * TIA dashboard (overview for HR / IT).
 *   GET /tia-dashboard?year=2026&quarter=2
 */
class TiaDashboardController extends BaseController
{
    /** Same cut-offs as the Annex D-1 / E-1 reports. VERIFY against AO 229. Highest first. */
    private const SCALE = [
        [3.50, 'High Impact'],
        [2.50, 'Moderate Impact'],
        [1.50, 'Low Impact'],
        [1.00, 'No Impact'],
    ];

    /** Training status => [label, icon, colour]. A training with no status counts as active. */
    private const STATUSES = [
        'active' => ['Active', 'bi-play-circle', 'success'],
        'rescheduled' => ['Rescheduled', 'bi-calendar2-week', 'warning'],
        'completed' => ['Completed', 'bi-check2-circle', 'primary'],
        'canceled' => ['Canceled', 'bi-x-circle', 'danger'],
    ];

    public function index()
    {
        $model = new TiaDashboardModel();

        $year = (int) $this->request->getGet('year');
        $quarter = (int) $this->request->getGet('quarter');
        $quarter = ($quarter >= 1 && $quarter <= 4) ? $quarter : 0;

        // ---------- Trainings (with their quarter) ----------
        $all = $model->trainings();
        $years = [(int) date('Y')];

        foreach ($all as &$t) {
            $t['_q'] = $this->quarterOf($t);
            $t['_status'] = $this->statusKey((string) ($t['status'] ?? ''));
            if ($t['_q'] !== '') {
                $years[] = (int) substr($t['_q'], 0, 4);
            }
        }
        unset($t);

        $years = array_values(array_unique($years));
        rsort($years);

        $trainings = array_values(array_filter($all, function (array $t) use ($year, $quarter): bool {
            if ($year > 0 && ($t['_q'] === '' || (int) substr($t['_q'], 0, 4) !== $year)) {
                return false;
            }
            if ($quarter > 0 && ($t['_q'] === '' || (int) substr($t['_q'], -1) !== $quarter)) {
                return false;
            }
            return true;
        }));

        // ---------- Counts per training ----------
        $participants = $model->participantCounts();
        $selfRows = $this->groupByCode($model->selfResponses());
        $supRows = $this->groupByCode($model->superiorResponses());

        $statusCounts = array_fill_keys(array_keys(self::STATUSES), 0);
        $quarterly = [];
        $attention = [];
        $totals = ['trainings' => count($trainings), 'participants' => 0, 'self' => 0, 'superior' => 0];

        // rating sums (only trainings that are not canceled)
        $selfSum = array_fill_keys(array_keys(TiaDashboardModel::SELF_SCORES), [0, 0]);
        $supSum = array_fill_keys(array_keys(TiaDashboardModel::SUPERIOR_SCORES), [0, 0]);

        foreach ($trainings as &$t) {
            $code = $t['training_code'];
            $statusCounts[$t['_status']]++;

            $t['_p'] = (int) ($participants[$code] ?? 0);
            $t['_self'] = count($selfRows[$code] ?? []);
            $t['_sup'] = count($supRows[$code] ?? []);

            // A canceled training does not count towards completion figures
            if ($t['_status'] === 'canceled') {
                continue;
            }

            $totals['participants'] += $t['_p'];
            $totals['self'] += $t['_self'];
            $totals['superior'] += $t['_sup'];

            $this->addScores($selfSum, $selfRows[$code] ?? []);
            $this->addScores($supSum, $supRows[$code] ?? []);

            // Quarterly table
            $qk = $t['_q'] !== '' ? $t['_q'] : 'Undated';
            $quarterly[$qk] ??= ['trainings' => 0, 'participants' => 0, 'self' => 0, 'superior' => 0];
            $quarterly[$qk]['trainings']++;
            $quarterly[$qk]['participants'] += $t['_p'];
            $quarterly[$qk]['self'] += $t['_self'];
            $quarterly[$qk]['superior'] += $t['_sup'];

            // Needs attention: participants who have not been rated / have not rated yet
            $pendingSelf = max(0, $t['_p'] - $t['_self']);
            $pendingSup = max(0, $t['_p'] - $t['_sup']);
            if ($t['_p'] > 0 && ($pendingSelf + $pendingSup) > 0) {
                $attention[] = $t + ['_pending_self' => $pendingSelf, '_pending_sup' => $pendingSup];
            }
        }
        unset($t);

        krsort($quarterly);

        usort($attention, fn($a, $b) => ($b['_pending_self'] + $b['_pending_sup']) <=> ($a['_pending_self'] + $a['_pending_sup']));

        $pct = static fn(int $n, int $d): int => $d > 0 ? (int) min(100, round($n / $d * 100)) : 0;

        $selfAvg = $this->average($selfSum);
        $supAvg = $this->average($supSum);

        return view('dashboard/tia_dashboard', [
            'filters' => ['year' => $year, 'quarter' => $quarter],
            'years' => $years,
            'totals' => $totals,
            'selfRate' => $pct($totals['self'], $totals['participants']),
            'supRate' => $pct($totals['superior'], $totals['participants']),
            'selfAvg' => $selfAvg,
            'selfRating' => $this->adjectival($selfAvg),
            'supAvg' => $supAvg,
            'supRating' => $this->adjectival($supAvg),
            'statuses' => self::STATUSES,
            'statusCounts' => $statusCounts,
            'quarterly' => $quarterly,
            'attention' => array_slice($attention, 0, 8),
            'attentionTotal' => count($attention),
            'latest' => array_slice($trainings, 0, 6),
        ]);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function statusKey(string $status): string
    {
        $s = strtolower(trim($status));
        return isset(self::STATUSES[$s]) ? $s : 'active';
    }

    private function groupByCode(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $out[$r['training_code']][] = $r;
        }
        return $out;
    }

    /** Add every 1-4 rating of these responses to the running [sum, count] of each category. */
    private function addScores(array &$sum, array $responses): void
    {
        foreach ($responses as $r) {
            foreach ($sum as $prefix => [$total, $count]) {
                // up to 5 items per category; a column the form does not have is simply null
                for ($i = 1; $i <= 5; $i++) {
                    $v = $r["{$prefix}_{$i}"] ?? null;
                    if ($v !== null && $v !== '' && (int) $v >= 1 && (int) $v <= 4) {
                        $total += (int) $v;
                        $count++;
                    }
                }
                $sum[$prefix] = [$total, $count];
            }
        }
    }

    /** Mean of the category means (a category with no ratings is skipped). */
    private function average(array $sum): ?float
    {
        $means = [];
        foreach ($sum as [$total, $count]) {
            if ($count > 0) {
                $means[] = $total / $count;
            }
        }
        return $means ? array_sum($means) / count($means) : null;
    }

    private function adjectival(?float $avg): string
    {
        if ($avg === null) {
            return '—';
        }
        foreach (self::SCALE as [$min, $label]) {
            if ($avg >= $min) {
                return $label;
            }
        }
        return '—';
    }

    /** "2026-Q2" from the training dates text (falls back to when it was added). */
    private function quarterOf(array $t): string
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $text = (string) ($t['training_dates'] ?? '');
        $created = strtotime((string) ($t['created_at'] ?? '')) ?: null;
        $month = 0;
        $year = 0;
        $m = [];
        $y = [];

        if (preg_match('/\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*/i', $text, $m)) {
            $month = (int) array_search(strtolower($m[1]), $months, true) + 1;
        }
        if (preg_match('/\b(20\d{2})\b/', $text, $y)) {
            $year = (int) $y[1];
        }
        if ($month === 0 && $created) {
            $month = (int) date('n', $created);
        }
        if ($year === 0 && $created) {
            $year = (int) date('Y', $created);
        }

        return ($month > 0 && $year > 0) ? $year . '-Q' . (int) ceil($month / 3) : '';
    }
}