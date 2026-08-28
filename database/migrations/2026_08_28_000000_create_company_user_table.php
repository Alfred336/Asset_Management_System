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
        Schema::create('company_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'company_id']);
        });

        // Attach existing users to all existing companies for backward compatibility
        $users = DB::table('users')->pluck('id');
        $companyIds = DB::table('companies')->pluck('id');

        if ($users->isNotEmpty() && $companyIds->isNotEmpty()) {
            $records = [];
            $now = now();
            foreach ($users as $userId) {
                foreach ($companyIds as $companyId) {
                    $records[] = [
                        'user_id' => $userId,
                        'company_id' => $companyId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DB::table('company_user')->insertOrIgnore($records);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};
