<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role_type', ['OWNER','LAUNDRY','FOOD']);

            // Application Details
            $table->string('business_name', 200);
            $table->string('business_registration', 100)->nullable();
            $table->string('document_path', 500)->nullable();

            // Contact Details
            $table->string('contact_person', 100);
            $table->string('contact_email', 150);
            $table->string('contact_phone', 20);

            // Location for service providers
            $table->text('business_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('service_radius_km', 5, 2)->nullable();

            // Status
            $table->enum('status', ['PENDING','APPROVED','REJECTED'])->default('PENDING');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_applications');
    }
};
