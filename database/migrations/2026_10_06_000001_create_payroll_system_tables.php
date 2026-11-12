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
        // 1. Employee Salary Structures
        if (!Schema::hasTable('salary_structures')) {
            Schema::create('salary_structures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('effective_from');
                $table->decimal('ctc_annual', 12, 2)->default(0);
                $table->decimal('monthly_gross', 12, 2)->default(0);
                
                // Earnings Breakdown
                $table->decimal('basic_salary', 12, 2)->default(0);
                $table->decimal('hra', 12, 2)->default(0);
                $table->decimal('conveyance_allowance', 12, 2)->default(0);
                $table->decimal('medical_allowance', 12, 2)->default(0);
                $table->decimal('special_allowance', 12, 2)->default(0);
                $table->decimal('other_allowance', 12, 2)->default(0);

                // Statutory Deductions
                $table->decimal('pf_employee', 12, 2)->default(0);
                $table->decimal('pf_employer', 12, 2)->default(0);
                $table->decimal('esi_employee', 12, 2)->default(0);
                $table->decimal('esi_employer', 12, 2)->default(0);
                $table->decimal('professional_tax', 12, 2)->default(0);
                $table->decimal('tds_monthly', 12, 2)->default(0);

                // Banking & Statutory Identifiers
                $table->string('bank_name', 100)->nullable();
                $table->string('bank_account_no', 50)->nullable();
                $table->string('ifsc_code', 20)->nullable();
                $table->string('pan_no', 20)->nullable();
                $table->string('uan_no', 30)->nullable();
                $table->string('pf_no', 50)->nullable();
                $table->string('esi_no', 50)->nullable();

                $table->enum('payment_mode', ['Bank Transfer', 'Cheque', 'Cash'])->default('Bank Transfer');
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        // 2. Monthly Payroll Batches
        if (!Schema::hasTable('payroll_batches')) {
            Schema::create('payroll_batches', function (Blueprint $table) {
                $table->id();
                $table->string('batch_number', 50)->unique();
                $table->unsignedTinyInteger('month'); // 1 - 12
                $table->unsignedSmallInteger('year'); // e.g. 2026
                $table->unsignedInteger('total_employees')->default(0);
                $table->decimal('total_gross', 14, 2)->default(0);
                $table->decimal('total_deductions', 14, 2)->default(0);
                $table->decimal('total_net_pay', 14, 2)->default(0);
                $table->enum('status', ['Draft', 'Generated', 'Approved', 'Paid'])->default('Generated');
                $table->date('payment_date')->nullable();
                $table->string('payment_mode', 50)->nullable();
                $table->string('transaction_reference', 100)->nullable();
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Individual Employee Payslips
        if (!Schema::hasTable('payslips')) {
            Schema::create('payslips', function (Blueprint $table) {
                $table->id();
                $table->string('payslip_number', 50)->unique();
                $table->unsignedBigInteger('payroll_batch_id')->nullable()->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->unsignedTinyInteger('month');
                $table->unsignedSmallInteger('year');

                // Attendance Metrics
                $table->unsignedTinyInteger('total_days')->default(30);
                $table->decimal('present_days', 4, 1)->default(0);
                $table->decimal('paid_leave_days', 4, 1)->default(0);
                $table->decimal('holiday_days', 4, 1)->default(0);
                $table->decimal('weekly_off_days', 4, 1)->default(0);
                $table->decimal('lwp_days', 4, 1)->default(0);
                $table->decimal('payable_days', 4, 1)->default(0);

                // Earnings Snapshot
                $table->decimal('base_gross', 12, 2)->default(0);
                $table->decimal('earned_basic', 12, 2)->default(0);
                $table->decimal('earned_hra', 12, 2)->default(0);
                $table->decimal('earned_conveyance', 12, 2)->default(0);
                $table->decimal('earned_medical', 12, 2)->default(0);
                $table->decimal('earned_special_allowance', 12, 2)->default(0);
                $table->decimal('bonus', 12, 2)->default(0);
                $table->decimal('incentive', 12, 2)->default(0);
                $table->decimal('overtime_pay', 12, 2)->default(0);
                $table->decimal('total_earnings', 12, 2)->default(0);

                // Deductions Snapshot
                $table->decimal('pf_deduction', 12, 2)->default(0);
                $table->decimal('esi_deduction', 12, 2)->default(0);
                $table->decimal('pt_deduction', 12, 2)->default(0);
                $table->decimal('tds_deduction', 12, 2)->default(0);
                $table->decimal('lwp_deduction', 12, 2)->default(0);
                $table->decimal('other_deductions', 12, 2)->default(0);
                $table->decimal('total_deductions', 12, 2)->default(0);

                // Net Pay
                $table->decimal('net_salary', 12, 2)->default(0);
                $table->string('net_salary_in_words', 255)->nullable();

                // Bank & Metadata Snapshot
                $table->string('bank_name', 100)->nullable();
                $table->string('bank_account_no', 50)->nullable();
                $table->string('ifsc_code', 20)->nullable();
                $table->string('pan_no', 20)->nullable();
                $table->string('uan_no', 30)->nullable();
                $table->string('designation_name', 100)->nullable();
                $table->string('department_name', 100)->nullable();

                // Payment Status
                $table->enum('payment_status', ['Pending', 'Paid'])->default('Pending');
                $table->date('payment_date')->nullable();
                $table->string('payment_mode', 50)->nullable();
                $table->string('transaction_reference', 100)->nullable();
                $table->string('pdf_path', 255)->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_batches');
        Schema::dropIfExists('salary_structures');
    }
};
