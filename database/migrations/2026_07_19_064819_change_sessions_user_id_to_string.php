<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * `user_id` was created as `foreignId()` (unsigned bigint), sized for an auto-increment users
     * table that this app deliberately doesn't have. App\Auth\AdminUser::getAuthIdentifier()
     * returns the admin's email string, so every session write once a user is authenticated
     * failed with a QueryException (non-numeric string into a bigint column) - silently caught
     * by DatabaseSessionHandler::performInsert()'s fallback to performUpdate(), which no-ops
     * because the row was never inserted. That made the session (and therefore the CSRF token)
     * never actually persist past login, causing every subsequent POST to 419.
     */
    public function up(): void
    {
        // SQLite (used by the test suite) has no real column types to fix - it stores whatever is
        // given regardless of the declared type, so this never breaks there. Only MySQL needs the fix.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE sessions MODIFY user_id VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL');
        }
    }
};
