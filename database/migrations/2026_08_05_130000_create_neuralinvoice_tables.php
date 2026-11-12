<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('countrymaster')) {
            Schema::create('countrymaster', function (Blueprint $table) {
                $table->increments('CountryID');
                $table->string('CountryName', 100);
            });
        }

        if (!Schema::hasTable('statemaster')) {
            Schema::create('statemaster', function (Blueprint $table) {
                $table->increments('StateID');
                $table->string('StateName', 45);
                $table->unsignedInteger('CountryID');
            });
        }

        if (!Schema::hasTable('citymaster')) {
            Schema::create('citymaster', function (Blueprint $table) {
                $table->increments('CityId');
                $table->string('CityName', 45);
                $table->unsignedInteger('StateId');
            });
        }

        if (!Schema::hasTable('companymaster')) {
            Schema::create('companymaster', function (Blueprint $table) {
                $table->increments('CompanyID');
                $table->string('CompanyName', 150);
                $table->string('CompanyCode', 50)->nullable();
                $table->string('CompanyLogo')->nullable();
                $table->string('EmailAddress', 150)->nullable();
                $table->string('MobileNo', 30)->nullable();
                $table->string('PhoneNo', 30)->nullable();
                $table->string('Website', 150)->nullable();
                $table->text('Address')->nullable();
                $table->string('RegistrationNo', 45)->nullable();
                $table->string('PanNo', 25)->nullable();
                $table->string('GSTNumber', 25)->nullable();
                $table->string('IECNo', 30)->nullable();
                $table->string('LUTNo', 50)->nullable();
                $table->string('TANNo', 30)->nullable();
                $table->string('Bank1AccountName', 150)->nullable();
                $table->string('Bank1AccountNo', 50)->nullable();
                $table->string('Bank1BankName', 150)->nullable();
                $table->string('Bank1IFSC', 30)->nullable();
                $table->string('Bank1MICR', 30)->nullable();
                $table->string('Bank1SwiftCode', 30)->nullable();
                $table->string('Bank1Branch', 150)->nullable();
                $table->string('Bank1BranchCode', 30)->nullable();
                $table->string('Bank2AccountName', 150)->nullable();
                $table->string('Bank2AccountNo', 50)->nullable();
                $table->string('Bank2BankName', 150)->nullable();
                $table->string('Bank2IFSC', 30)->nullable();
                $table->tinyInteger('DelStatus')->default(1);
                $table->unique('CompanyName', 'uk_company_name');
            });
        }

        if (!Schema::hasTable('clientmaster')) {
            Schema::create('clientmaster', function (Blueprint $table) {
                $table->increments('ClientID');
                $table->integer('CompanyID')->default(0);
                $table->integer('ProductCategoryType')->nullable();
                $table->integer('CategoryType')->nullable();
                $table->text('ClientCode');
                $table->string('Priority', 5);
                $table->integer('Source')->nullable();
                $table->string('ClientName', 50);
                $table->string('ConcernOwner', 40);
                $table->string('Address', 100);
                $table->string('MobileNo', 15);
                $table->string('Fax', 15);
                $table->text('website');
                $table->string('client_logo')->nullable();
                $table->string('gst_number')->default('');
                $table->string('pinNo', 15)->nullable();
                $table->string('EmailAddress', 150)->default('');
                $table->string('Country', 40);
                $table->string('StateID', 11)->nullable();
                $table->string('PhoneNo', 30)->nullable();
                $table->integer('DelStatus')->default(0);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('invoice_clients')) {
            Schema::create('invoice_clients', function (Blueprint $table) {
                $table->increments('id');
                $table->date('invoiceDate');
                $table->string('client_name');
                $table->string('concern_person');
                $table->string('cin');
                $table->string('website');
                $table->string('email');
                $table->string('mobile', 20);
                $table->integer('country_id');
                $table->integer('state_id');
                $table->text('address');
                $table->string('gst_number');
            });
        }

        if (!Schema::hasTable('work_type_master')) {
            Schema::create('work_type_master', function (Blueprint $table) {
                $table->increments('WorkTypeID');
                $table->string('WorkTypeName', 100);
                $table->enum('amc_type', ['project', 'amc_only'])->default('project');
                $table->tinyInteger('DelStatus')->default(1);
                $table->integer('sort_order')->default(0);
                $table->dateTime('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('client_projects')) {
            Schema::create('client_projects', function (Blueprint $table) {
                $table->increments('project_id');
                $table->integer('client_id')->index('idx_client_projects_client');
                $table->string('project_name');
                $table->enum('engagement_type', ['project', 'amc_only'])->default('project');
                $table->enum('has_amc', ['yes', 'no'])->default('no');
                $table->enum('payment_mode', ['percentage', 'module'])->default('percentage');
                $table->decimal('total_value', 15, 2)->default(0);
                $table->decimal('tax_percent', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('cgst_percent', 5, 2)->default(0);
                $table->decimal('cgst_amount', 12, 2)->default(0);
                $table->decimal('sgst_percent', 5, 2)->default(0);
                $table->decimal('sgst_amount', 12, 2)->default(0);
                $table->decimal('igst_percent', 5, 2)->default(0);
                $table->decimal('igst_amount', 12, 2)->default(0);
                $table->string('tax_type', 50)->nullable();
                $table->decimal('total_value_with_tax', 12, 2)->default(0);
                $table->decimal('total_with_tax', 12, 2)->default(0);
                $table->decimal('final_amount', 12, 2)->default(0);
                $table->date('amc_start')->nullable();
                $table->date('amc_end')->nullable();
                $table->date('project_start_date')->nullable();
                $table->date('project_end_date')->nullable();
                $table->string('financial_year', 12)->nullable();
                $table->integer('amc_working_days')->default(0);
                $table->integer('work_type_id')->nullable();
                $table->tinyInteger('DelStatus')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('project_payment_milestones')) {
            Schema::create('project_payment_milestones', function (Blueprint $table) {
                $table->increments('milestone_id');
                $table->integer('project_id');
                $table->string('milestone_label');
                $table->string('module_name')->nullable();
                $table->decimal('percentage', 6, 2)->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('amount_with_tax', 12, 2)->default(0);
                $table->decimal('invoiced_amount', 12, 2)->default(0);
                $table->decimal('cgst_amount', 15, 2)->default(0);
                $table->decimal('sgst_amount', 15, 2)->default(0);
                $table->decimal('igst_amount', 15, 2)->default(0);
                $table->decimal('total_with_tax', 15, 2)->default(0);
                $table->date('due_date')->nullable();
                $table->tinyInteger('is_paid')->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->decimal('partial_paid', 12, 2)->default(0);
                $table->date('paid_date')->nullable();
                $table->date('last_paid_date')->nullable();
                $table->integer('sort_order')->default(0);
            });
        }

        if (!Schema::hasTable('invoice')) {
            Schema::create('invoice', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('invoice_type');
                $table->integer('ClientID');
                $table->integer('CompanyID')->nullable();
                $table->integer('milestone_id')->nullable();
                $table->date('invoiceDate');
                $table->string('concern', 50);
                $table->string('mobile', 20);
                $table->string('terms');
                $table->date('deliverydate');
                $table->date('duedate');
                $table->string('duration_days', 100)->nullable();
                $table->integer('sacCode');
                $table->integer('subtotal');
                $table->integer('discount');
                $table->integer('finalAmount');
                $table->integer('sgstAmount')->nullable();
                $table->integer('cgstAmount')->nullable();
                $table->integer('igstAmount')->nullable();
                $table->decimal('bankChargePercent', 5, 2)->default(0);
                $table->decimal('bankChargeAmount', 15, 2)->default(0);
                $table->integer('totalAmount');
                $table->string('currency', 10)->default('INR');
                $table->decimal('exchange_rate', 10, 4)->default(1);
                $table->decimal('total_amount_foreign', 15, 2)->default(0);
                $table->integer('status')->default(0);
                $table->dateTime('sent_at')->nullable();
                $table->dateTime('cancelled_at')->nullable();
                $table->string('cancel_reason')->nullable();
                $table->date('creation_date');
            });
        }

        if (!Schema::hasTable('workamount')) {
            Schema::create('workamount', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('invoice_id')->index();
                $table->string('invoiceNumber', 50);
                $table->string('taxinvoiceNumber', 50)->nullable();
                $table->string('description');
                $table->integer('amount');
                $table->string('duration', 100)->nullable();
            });
        }

        if (!Schema::hasTable('recived_payment')) {
            Schema::create('recived_payment', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('ClientID');
                $table->integer('workAmount_invoice_id')->index();
                $table->string('taxinvoiceNumber', 50);
                $table->decimal('recived_amount', 15, 2)->default(0);
                $table->decimal('tds_amount', 15, 2)->default(0);
                $table->decimal('received_net', 15, 2)->default(0);
                $table->string('payment_note')->nullable();
                $table->dateTime('received_date')->nullable();
                $table->integer('status');
                $table->date('created_at');
            });
        }

        if (!Schema::hasTable('expense_categories')) {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('expense_categories_id')->nullable();
                $table->string('categories', 50);
                $table->integer('status');
                $table->date('created_at');
                $table->date('updated_at');
            });
        }

        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('expense_category_id');
                $table->string('expense_name');
                $table->decimal('expense_cost', 10, 2);
                $table->date('expense_date');
                $table->enum('expense_mode', ['Cash', 'Online']);
                $table->string('expense_invoice')->nullable();
                $table->integer('expense_status')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('invoiceemployees')) {
            Schema::create('invoiceemployees', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username');
                $table->string('password');
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'invoiceemployees',
            'expenses',
            'expense_categories',
            'recived_payment',
            'workamount',
            'invoice',
            'project_payment_milestones',
            'client_projects',
            'work_type_master',
            'invoice_clients',
            'clientmaster',
            'companymaster',
            'citymaster',
            'statemaster',
            'countrymaster',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
