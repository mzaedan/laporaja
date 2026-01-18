<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Priority scoring criteria (1-5 scale)
            $table->tinyInteger('community_impact_score')->default(1)->after('urgency_level')->comment('Dampak ke masyarakat (1-5)');
            $table->tinyInteger('damage_level_score')->default(1)->after('community_impact_score')->comment('Tingkat kerusakan (1-5)');
            $table->tinyInteger('time_urgency_score')->default(1)->after('damage_level_score')->comment('Urgensi waktu penanganan (1-5)');
            
            // Calculated priority score
            $table->decimal('calculated_priority_score', 3, 2)->default(1.00)->after('time_urgency_score')->comment('Nilai prioritas terhitung (1.00-5.00)');
            
            // Metadata for scoring
            $table->text('scoring_notes')->nullable()->after('calculated_priority_score')->comment('Catatan penilaian kriteria');
            $table->timestamp('priority_calculated_at')->nullable()->after('scoring_notes')->comment('Waktu perhitungan prioritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'community_impact_score',
                'damage_level_score', 
                'time_urgency_score',
                'calculated_priority_score',
                'scoring_notes',
                'priority_calculated_at'
            ]);
        });
    }
};
