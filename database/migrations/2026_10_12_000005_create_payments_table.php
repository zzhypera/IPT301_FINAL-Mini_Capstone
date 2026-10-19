<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('job_id')->constrained('repair_jobs', 'job_id')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method', 30)->default('Cash');
            $table->string('reference_number', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
