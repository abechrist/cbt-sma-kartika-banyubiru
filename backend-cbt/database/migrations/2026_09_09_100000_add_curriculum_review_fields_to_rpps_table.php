<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            if (! Schema::hasColumn('rpps', 'review_status')) {
                $table->string('review_status', 30)->default('pending')->after('status');
                $table->text('review_notes')->nullable()->after('review_status');
                $table->foreignId('reviewed_by')->nullable()->after('review_notes')->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });

        if (Schema::hasTable('roles')) {
            Role::firstOrCreate(
                ['name' => 'koordinator_kurikulum'],
                ['description' => 'Guru Koordinator Kurikulum - Mengajar serta memantau perencanaan & pelaksanaan kurikulum guru']
            );
        }
    }

    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            if (Schema::hasColumn('rpps', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }
            $columns = array_filter(['review_status', 'review_notes', 'reviewed_at'], fn ($col) => Schema::hasColumn('rpps', $col));
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
