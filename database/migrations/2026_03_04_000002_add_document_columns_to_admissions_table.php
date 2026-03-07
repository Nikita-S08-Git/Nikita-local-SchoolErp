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
        Schema::table('admissions', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('application_fee_paid');
            $table->string('signature_path')->nullable()->after('photo_path');
            $table->string('twelfth_marksheet_path')->nullable()->after('signature_path');
            $table->string('cast_certificate_path')->nullable()->after('twelfth_marksheet_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path',
                'signature_path',
                'twelfth_marksheet_path',
                'cast_certificate_path',
            ]);
        });
    }
};
