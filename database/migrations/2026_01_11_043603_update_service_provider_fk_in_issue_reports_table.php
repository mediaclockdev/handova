<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 1️⃣ Remove invalid data FIRST (mandatory)
        DB::statement("
            UPDATE issue_reports
            SET service_provider = NULL
            WHERE service_provider IS NOT NULL
            AND service_provider NOT IN (SELECT id FROM users)
        ");

        // 2️⃣ Drop FK ONLY if it exists (MySQL-safe)
        DB::statement("
            ALTER TABLE issue_reports
            DROP FOREIGN KEY IF EXISTS issue_reports_service_provider_foreign
        ");

        // 3️⃣ Add correct FK
        DB::statement("
            ALTER TABLE issue_reports
            ADD CONSTRAINT issue_reports_service_provider_foreign
            FOREIGN KEY (service_provider)
            REFERENCES users(id)
            ON DELETE SET NULL
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE issue_reports
            DROP FOREIGN KEY IF EXISTS issue_reports_service_provider_foreign
        ");

        DB::statement("
            ALTER TABLE issue_reports
            ADD CONSTRAINT issue_reports_service_provider_foreign
            FOREIGN KEY (service_provider)
            REFERENCES service_providers(id)
            ON DELETE SET NULL
        ");
    }
};
