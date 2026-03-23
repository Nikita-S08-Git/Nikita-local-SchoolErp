<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\AdmissionService;
use App\Models\Academic\Admission;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\User;
use App\Models\AuditLog;
use Exception;

class AdmissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AdmissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdmissionService();
    }

    public function test_apply_creates_admission(): void
    {
        $program = Program::factory()->create();
        $division = Division::factory()->create();
        $academicSession = AcademicSession::factory()->create();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'program_id' => $program->id,
            'division_id' => $division->id,
            'academic_session_id' => $academicSession->id,
            'mobile_number' => '1234567890',
            'email' => 'john@example.com',
        ];

        $admission = $this->service->apply($data);

        $this->assertInstanceOf(Admission::class, $admission);
        $this->assertDatabaseHas('admissions', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'status' => 'applied'
        ]);
        $this->assertNotNull($admission->application_no);
    }

    public function test_apply_generates_unique_application_number(): void
    {
        $program = Program::factory()->create();
        $division = Division::factory()->create();
        $academicSession = AcademicSession::factory()->create();

        $data1 = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'program_id' => $program->id,
            'division_id' => $division->id,
            'academic_session_id' => $academicSession->id,
            'mobile_number' => '1234567890',
            'email' => 'john1@example.com',
        ];

        $data2 = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'date_of_birth' => '2010-01-02',
            'gender' => 'female',
            'program_id' => $program->id,
            'division_id' => $division->id,
            'academic_session_id' => $academicSession->id,
            'mobile_number' => '1234567891',
            'email' => 'john2@example.com',
        ];

        $admission1 = $this->service->apply($data1);
        $admission2 = $this->service->apply($data2);

        $this->assertNotEquals($admission1->application_no, $admission2->application_no);
    }

    public function test_apply_runs_inside_transaction(): void
    {
        $program = Program::factory()->create();
        $division = Division::factory()->create();
        $academicSession = AcademicSession::factory()->create();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'program_id' => $program->id,
            'division_id' => $division->id,
            'academic_session_id' => $academicSession->id,
            'mobile_number' => '1234567890',
            'email' => 'transaction@test.com',
        ];

        $admission = $this->service->apply($data);

        // Verify audit log was created (transaction committed)
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Admission::class,
            'auditable_id' => $admission->id,
            'event' => 'applied'
        ]);
    }

    public function test_verify_admission_sets_status_to_verified(): void
    {
        $admission = Admission::factory()->create([
            'status' => 'applied',
            'application_fee_paid' => true
        ]);

        // Authenticate for the verifyAdmission method
        $this->actingAs(User::factory()->create());

        $result = $this->service->verifyAdmission($admission);

        $this->assertEquals('verified', $result->status);
        $this->assertNotNull($result->verified_at);
        $this->assertNotNull($result->verified_by);
    }

    public function test_verify_admission_throws_exception_when_not_applied(): void
    {
        $admission = Admission::factory()->create([
            'status' => 'verified'
        ]);

        $this->actingAs(User::factory()->create());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Admission cannot be verified');

        $this->service->verifyAdmission($admission);
    }

    public function test_verify_admission_throws_exception_without_application_fee(): void
    {
        $admission = Admission::factory()->create([
            'status' => 'applied',
            'application_fee_paid' => false
        ]);

        $this->actingAs(User::factory()->create());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Admission cannot be verified');

        $this->service->verifyAdmission($admission);
    }

    public function test_reject_admission_sets_status_to_rejected(): void
    {
        $admission = Admission::factory()->create([
            'status' => 'applied'
        ]);

        $this->actingAs(User::factory()->create());

        $reason = 'Incomplete documents';
        $result = $this->service->rejectAdmission($admission, $reason);

        $this->assertEquals('rejected', $result->status);
        $this->assertEquals($reason, $result->rejection_reason);
    }

    public function test_reject_admission_logs_the_event(): void
    {
        $admission = Admission::factory()->create([
            'status' => 'applied'
        ]);

        $this->actingAs(User::factory()->create());

        $this->service->rejectAdmission($admission, 'Test rejection');

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Admission::class,
            'auditable_id' => $admission->id,
            'event' => 'rejected'
        ]);
    }

    public function test_get_admission_stats_returns_correct_counts(): void
    {
        Admission::factory()->count(3)->create(['status' => 'applied']);
        Admission::factory()->count(2)->create(['status' => 'verified']);
        Admission::factory()->count(1)->create(['status' => 'rejected']);

        $stats = $this->service->getAdmissionStats();

        $this->assertEquals(6, $stats['total']);
        $this->assertEquals(3, $stats['applied']);
        $this->assertEquals(2, $stats['verified']);
        $this->assertEquals(1, $stats['rejected']);
    }

    public function test_validation_is_handled_at_controller_level(): void
    {
        // This test verifies that the service doesn't handle validation
        // Validation should be handled in the controller or form request
        
        $program = Program::factory()->create();
        $division = Division::factory()->create();
        $academicSession = AcademicSession::factory()->create();

        // Missing required fields - service should not validate
        // Instead, it will try to create and fail at database level
        $data = [
            'first_name' => 'John',
            // missing last_name, date_of_birth, etc.
            'program_id' => $program->id,
            'division_id' => $division->id,
            'academic_session_id' => $academicSession->id,
            'mobile_number' => '1234567890',
        ];

        // This should throw a database exception, not a validation exception
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $this->service->apply($data);
    }
}
