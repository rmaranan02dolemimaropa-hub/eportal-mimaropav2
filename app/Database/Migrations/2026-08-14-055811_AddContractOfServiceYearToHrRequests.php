<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContractOfServiceYearToHrRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('hr_requests', [
            'contract_of_service_year' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
                'null' => true,
                'after' => 'ipcr_period',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('hr_requests', 'contract_of_service_year');
    }
}