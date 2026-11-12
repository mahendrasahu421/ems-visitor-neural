<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\LeaveType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTypeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure scopeActive returns only active leave types.
     */
    public function test_scope_active_returns_only_active_leave_types(): void
    {
        // Arrange
        $company = Company::factory()->create();
        LeaveType::factory()->for($company)->create(['status' => true]);
        LeaveType::factory()->for($company)->create(['status' => false]);

        // Act
        $active = LeaveType::query()->active()->get();

        // Assert
        $this->assertCount(1, $active);
        $this->assertTrue((bool) $active->first()->status);
    }

    /**
     * Ensure the company() relationship is defined as belongsTo Company.
     */
    public function test_company_relationship(): void
    {
        $company = Company::factory()->create();
        $leaveType = LeaveType::factory()->for($company)->create();

        $this->assertTrue($leaveType->relationLoaded('company') === false);
        $this->assertInstanceOf(Company::class, $leaveType->company()->getModel());
        $this->assertTrue($leaveType->company()->exists());
        $this->assertEquals($company->id, $leaveType->company->id);
    }

    /**
     * Ensure leaves() relationship exists and is hasMany.
     */
    public function test_leaves_relationship_exists(): void
    {
        $leaveType = LeaveType::factory()->create();

        $relation = $leaveType->leaves();

        $this->assertTrue(method_exists($leaveType, 'leaves'));
        $this->assertEquals('Illuminate\\Database\\Eloquent\\Relations\\HasMany', get_class($relation));
    }

    /**
     * Creating a LeaveType should allow mass assignment of defined attributes.
     */
    public function test_mass_assignment_on_fillable_fields(): void
    {
        $data = [
            'name' => 'Annual Leave',
            'description' => 'Yearly paid leave',
            'days' => 12,
            'status' => true,
        ];

        $leaveType = LeaveType::create($data);

        $this->assertDatabaseHas('leave_types', [
            'name' => 'Annual Leave',
            'description' => 'Yearly paid leave',
            'days' => 12,
            'status' => true,
        ]);

        $this->assertEquals('Annual Leave', $leaveType->name);
        $this->assertEquals('Yearly paid leave', $leaveType->description);
        $this->assertEquals(12, $leaveType->days);
        $this->assertTrue((bool) $leaveType->status);
    }

    /**
     * Ensure scopeActive works with zero records and does not error.
     */
    public function test_scope_active_with_no_records_returns_empty_collection(): void
    {
        $active = LeaveType::query()->active()->get();
        $this->assertCount(0, $active);
    }
}
