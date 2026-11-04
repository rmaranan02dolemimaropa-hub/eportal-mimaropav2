<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\TiaAttention;
use App\Models\HrRequestModel;

/**
 * Combined dashboard (HR + TIA): Needs Attention and Quick Actions.
 * Route: overview  (the existing 'dashboard' route / DashboardController is untouched)
 */
class OverviewController extends BaseController
{
    // A request is "overdue" when it has sat in its status this many days
    private const NEW_OVERDUE_DAYS = 3;
    private const PROCESSING_OVERDUE_DAYS = 7;
    private const ATTENTION_LIMIT = 8;

    public function index()
    {
        $response = service('response');
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');

        $data = $this->hrAttention();

        // TIA: 'attention' (top rows) + 'attentionTotal', same rules as the TIA dashboard
        $data += (new TiaAttention())->get(self::ATTENTION_LIMIT);

        return view('dashboard/combined_dashboard', $data);
    }

    /**
     * Overdue HR requests across ALL pages:
     *  - New Application   waiting NEW_OVERDUE_DAYS or more (from date_requested)
     *  - Processing        waiting PROCESSING_OVERDUE_DAYS or more (from date_processing,
     *                      falling back to date_requested when that is empty)
     * Returns the oldest ATTENTION_LIMIT rows plus the full count.
     */
    private function hrAttention(): array
    {
        // Full timestamps, so the cutoffs match the view's age calculation
        $newCutoff = date('Y-m-d H:i:s', strtotime('-' . self::NEW_OVERDUE_DAYS . ' days'));
        $procCutoff = date('Y-m-d H:i:s', strtotime('-' . self::PROCESSING_OVERDUE_DAYS . ' days'));

        $model = new HrRequestModel();

        $model->groupStart()
            ->groupStart()
            ->where('status', 'New Application')
            ->where('date_requested <=', $newCutoff)
            ->groupEnd()
            ->orGroupStart()
            ->where('status', 'Processing')
            ->where('COALESCE(NULLIF(date_processing, \'0000-00-00 00:00:00\'), date_requested) <=', $procCutoff)
            ->groupEnd()
            ->groupEnd();

        // No LIMIT here: the view sorts by age and keeps the 8 oldest
        $rows = $model->orderBy('date_requested', 'ASC')->findAll();

        return [
            'hr_attention' => $rows,
            'hr_attention_total' => count($rows),
        ];
    }
}