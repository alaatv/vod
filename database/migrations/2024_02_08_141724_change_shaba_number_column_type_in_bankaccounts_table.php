<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->string('new_shaba', 191)->nullable();
        });

        // Copy data from the old column to the new one
        DB::table('bankaccounts')->update(['new_shaba' => DB::raw('shabaNumber')]);

        // Drop the old column
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->dropColumn('shabaNumber');
        });

        // Rename the new column to match the old one
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->renameColumn('new_shaba', 'shabaNumber');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add a new column with the old type
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->text('new_shaba')->nullable();
        });

        // Copy data from the new column to the old one
        DB::table('bankaccounts')->update(['new_shaba' => DB::raw('shabaNumber')]);

        // Drop the old column
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->dropColumn('shabaNumber');
        });

        // Rename the new column to match the old one
        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->renameColumn('new_shaba', 'shabaNumber');
        });
    }
};
