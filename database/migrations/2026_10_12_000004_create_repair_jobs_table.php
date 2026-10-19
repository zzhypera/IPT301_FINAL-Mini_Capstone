<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id('job_id');
            $table->foreignId('device_id')->constrained('devices', 'device_id')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('technicians', 'technician_id')->nullOnDelete();
            $table->string('issue_description', 1000);
            $table->string('status', 30)->default('Pending');
            $table->text('diagnosis')->nullable();
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->decimal('parts_cost', 10, 2)->default(0);
            $table->date('received_date');
            $table->date('completed_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_jobs');
    }
};
