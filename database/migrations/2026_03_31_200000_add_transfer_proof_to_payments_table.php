<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transfer_proof')->nullable()->after('invoice_emailed_at');
            $table->timestamp('transfer_proof_uploaded_at')->nullable()->after('transfer_proof');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['transfer_proof', 'transfer_proof_uploaded_at']);
        });
    }
};
