<?php

/**
 * =============================================================================
 * ACADEMIC RULES TABLE MIGRATION
 * =============================================================================
 *
 * This migration creates the 'academic_rules' table for storing configurable
 * academic rules. This allows institutions to define their own pass/fail/ATKT
 * criteria without code changes.
 *
 * PURPOSE:
 * - Store academic rules in database (not hardcoded)
 * - Enable institution-specific rule configuration
 * - Support rule versioning and effective dates
 * - Provide audit trail for rule changes
 *
 * KNOWLEDGE BASE COMPLIANCE:
 * - Rule engine architecture (knowledge_base.md §2)
 * - Configurable pass criteria (knowledge_base.md §5)
 * - Configurable ATKT rules (knowledge_base.md §5)
 * - Institution-level flexibility (knowledge_base.md §12)
 * =============================================================================
 */

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
        Schema::create('academic_rules', function (Blueprint $table) {
            // Primary key
            $table->id();

            // ========================================
            // RULE IDENTIFICATION
            // ========================================
            // Unique rule code (e.g., 'PASS_PERCENTAGE', 'ATKT_MAX_SUBJECTS')
            $table->string('rule_code', 50)->unique();

            // Human-readable rule name
            $table->string('name', 100);

            // Detailed rule description
            $table->text('description')->nullable();

            // ========================================
            // RULE CATEGORY
            // ========================================
            // Category for grouping rules
            $table->enum('category', [
                'result',
                'attendance',
                'promotion',
                'fee',
                'atkt',
                'examination',
                'general'
            ])->default('general');

            // ========================================
            // RULE TYPE
            // ========================================
            // Type of value stored
            $table->enum('value_type', [
                'boolean',
                'integer',
                'decimal',
                'string',
                'json',
                'array'
            ])->default('string');

            // ========================================
            // RULE VALUE
            // ========================================
            // The actual rule value (stored as string, cast based on value_type)
            $table->text('value');

            // Default value for this rule
            $table->text('default_value')->nullable();

            // ========================================
            // VALIDATION CONSTRAINTS
            // ========================================
            // Minimum allowed value (for numeric rules)
            $table->string('min_value', 50)->nullable();

            // Maximum allowed value (for numeric rules)
            $table->string('max_value', 50)->nullable();

            // Allowed values (for enum-like rules, stored as JSON array)
            $table->json('allowed_values')->nullable();

            // Validation regex pattern (for string rules)
            $table->string('validation_pattern', 255)->nullable();

            // ========================================
            // EFFECTIVE DATES
            // ========================================
            // When this rule becomes effective
            $table->date('effective_from')->nullable();

            // When this rule expires (null = no expiry)
            $table->date('effective_to')->nullable();

            // ========================================
            // RULE STATUS
            // ========================================
            // Is this rule currently active?
            $table->boolean('is_active')->default(true);

            // Is this rule mandatory (cannot be disabled)?
            $table->boolean('is_mandatory')->default(false);

            // Is this rule institution-specific or system-wide?
            $table->boolean('is_institution_specific')->default(true);

            // ========================================
            // PRIORITY & ORDERING
            // ========================================
            // Rule evaluation priority (lower = evaluated first)
            $table->integer('priority')->default(100);

            // Display order in UI
            $table->integer('display_order')->default(0);

            // ========================================
            // DEPENDENCIES
            // ========================================
            // Parent rule ID (if this rule depends on another)
            $table->foreignId('parent_rule_id')->nullable()->constrained('academic_rules')->onDelete('set null');

            // ========================================
            // METADATA
            // ========================================
            // Additional configuration (stored as JSON)
            $table->json('metadata')->nullable();

            // Tags for searching/filtering (stored as JSON array)
            $table->json('tags')->nullable();

            // ========================================
            // AUDIT FIELDS
            // ========================================
            // User who created this rule
            $table->foreignId('created_by')->nullable()->constrained('users');

            // User who last updated this rule
            $table->foreignId('updated_by')->nullable()->constrained('users');

            // User who approved this rule (if approval workflow enabled)
            $table->foreignId('approved_by')->nullable()->constrained('users');

            // When rule was approved
            $table->timestamp('approved_at')->nullable();

            // ========================================
            // TIMESTAMPS & SOFT DELETE
            // ========================================
            $table->timestamps();
            $table->softDeletes();

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            // Index for rule code lookup
            $table->index('rule_code');

            // Index for category filtering
            $table->index('category');

            // Index for active rules
            $table->index('is_active');

            // Index for effective date range queries
            $table->index(['effective_from', 'effective_to']);

            // Composite index for rule retrieval
            $table->index(['category', 'is_active', 'priority']);

            // Index for parent rule lookup
            $table->index('parent_rule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_rules');
    }
};
