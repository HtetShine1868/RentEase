<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_missing_property_owner_fields.php

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
        Schema::table('role_applications', function (Blueprint $table) {
            // Add only the columns that are definitely missing
            // (remove alt_phone since it already exists)
            
            if (!Schema::hasColumn('role_applications', 'property_type')) {
                $table->string('property_type')->nullable()->after('role_type');
            }
            
            if (!Schema::hasColumn('role_applications', 'property_count')) {
                $table->integer('property_count')->default(1)->after('property_type');
            }
            
            if (!Schema::hasColumn('role_applications', 'years_experience')) {
                $table->integer('years_experience')->default(0)->after('property_count');
            }
            
            if (!Schema::hasColumn('role_applications', 'id_document_path')) {
                $table->string('id_document_path')->nullable()->after('document_path');
            }
            
            if (!Schema::hasColumn('role_applications', 'property_documents')) {
                $table->json('property_documents')->nullable()->after('id_document_path');
            }
            
            if (!Schema::hasColumn('role_applications', 'property_names')) {
                $table->json('property_names')->nullable()->after('property_documents');
            }
            
            if (!Schema::hasColumn('role_applications', 'property_doc_types')) {
                $table->json('property_doc_types')->nullable()->after('property_names');
            }
            
            if (!Schema::hasColumn('role_applications', 'property_notes')) {
                $table->json('property_notes')->nullable()->after('property_doc_types');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_applications', function (Blueprint $table) {
            $columns = [
                'property_type',
                'property_count',
                'years_experience',
                'id_document_path',
                'property_documents',
                'property_names',
                'property_doc_types',
                'property_notes'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('role_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};