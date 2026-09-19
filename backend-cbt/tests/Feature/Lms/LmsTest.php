<?php

namespace Tests\Feature\Lms;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseDiscussion;
use App\Models\LearningMaterial;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $guru;

    protected User $siswa;

    protected User $siswaOtherClass;

    protected StudentClass $classA;

    protected StudentClass $classB;

    protected Subject $subject;

    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $roleSuper = Role::create(['name' => User::ROLE_SUPER_ADMIN, 'display_name' => 'Super Admin']);
        $roleGuru = Role::create(['name' => User::ROLE_GURU, 'display_name' => 'Guru']);
        $roleSiswa = Role::create(['name' => User::ROLE_SISWA, 'display_name' => 'Siswa']);

        $this->classA = StudentClass::create([
            'name' => 'X-A',
            'grade' => '10',
            'academic_year' => '2026/2027',
            'is_active' => true,
        ]);

        $this->classB = StudentClass::create([
            'name' => 'X-B',
            'grade' => '10',
            'academic_year' => '2026/2027',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'name' => 'Matematika',
            'code' => 'MTK',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => 'password',
            'role_id' => $roleSuper->id,
            'is_active' => true,
        ]);

        $this->guru = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'password' => 'password',
            'role_id' => $roleGuru->id,
            'is_active' => true,
        ]);

        $this->siswa = User::create([
            'name' => 'Ahmad Siswa',
            'email' => 'siswa@test.com',
            'password' => 'password',
            'role_id' => $roleSiswa->id,
            'class_id' => $this->classA->id,
            'nisn' => '1234567890',
            'is_active' => true,
        ]);

        $this->siswaOtherClass = User::create([
            'name' => 'Budi Lain Kelas',
            'email' => 'siswa2@test.com',
            'password' => 'password',
            'role_id' => $roleSiswa->id,
            'class_id' => $this->classB->id,
            'nisn' => '0987654321',
            'is_active' => true,
        ]);

        $this->course = Course::create([
            'subject_id' => $this->subject->id,
            'class_id' => $this->classA->id,
            'teacher_id' => $this->guru->id,
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);
    }

    public function test_guru_and_siswa_can_access_lms_courses_index(): void
    {
        $this->actingAs($this->guru)
            ->get(route('lms.courses.index'))
            ->assertStatus(200)
            ->assertSee('Kelas Pembelajaran Daring')
            ->assertSee('Matematika');

        $this->actingAs($this->siswa)
            ->get(route('lms.courses.index'))
            ->assertStatus(200)
            ->assertSee('Matematika');
    }

    public function test_guru_can_create_new_course(): void
    {
        $response = $this->actingAs($this->guru)->post(route('lms.courses.store'), [
            'subject_id' => $this->subject->id,
            'class_id' => $this->classB->id,
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'description' => 'Kelas Matematika X-B',
        ]);

        $response->assertRedirect(route('lms.courses.index'));
        $this->assertDatabaseHas('courses', [
            'subject_id' => $this->subject->id,
            'class_id' => $this->classB->id,
            'teacher_id' => $this->guru->id,
        ]);
    }

    public function test_siswa_can_access_their_course_but_forbidden_from_other_class(): void
    {
        // Siswa Class A accesses Course A -> OK
        $this->actingAs($this->siswa)
            ->get(route('lms.courses.show', $this->course->id))
            ->assertStatus(200)
            ->assertSee('Matematika');

        // Siswa Class B accesses Course A -> 403 Forbidden
        $this->actingAs($this->siswaOtherClass)
            ->get(route('lms.courses.show', $this->course->id))
            ->assertStatus(403);
    }

    public function test_guru_can_create_learning_material(): void
    {
        $response = $this->actingAs($this->guru)->post(route('lms.materials.store', $this->course->id), [
            'title' => 'Bab 1: Eksponen dan Logaritma',
            'chapter' => 'Bab 1',
            'type' => 'article',
            'content_text' => 'Ini materi penjelasan bab 1 eksponen...',
        ]);

        $response->assertRedirect(route('lms.courses.show', $this->course->id));
        $this->assertDatabaseHas('learning_materials', [
            'course_id' => $this->course->id,
            'title' => 'Bab 1: Eksponen dan Logaritma',
            'created_by' => $this->guru->id,
        ]);
    }

    public function test_siswa_can_view_material_and_toggle_completion(): void
    {
        $material = LearningMaterial::create([
            'course_id' => $this->course->id,
            'title' => 'Modul Bacaan Eksponen',
            'chapter' => 'Bab 1',
            'type' => 'article',
            'content_text' => 'Konten penjelasan materi.',
            'is_published' => true,
            'created_by' => $this->guru->id,
        ]);

        // Siswa views material
        $this->actingAs($this->siswa)
            ->get(route('lms.materials.show', [$this->course->id, $material->id]))
            ->assertStatus(200)
            ->assertSee('Modul Bacaan Eksponen');

        // Siswa toggles completion
        $this->actingAs($this->siswa)
            ->post(route('lms.materials.toggle-complete', [$this->course->id, $material->id]))
            ->assertSessionHas('success');

        $this->assertTrue($material->isCompletedBy($this->siswa->id));
    }

    public function test_guru_can_create_assignment(): void
    {
        $response = $this->actingAs($this->guru)->post(route('lms.assignments.store', $this->course->id), [
            'title' => 'Tugas 1 Eksponen',
            'instructions' => 'Selesaikan 5 butir soal di buku catatan.',
            'due_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'max_score' => 100,
        ]);

        $response->assertRedirect(route('lms.courses.show', $this->course->id));
        $this->assertDatabaseHas('assignments', [
            'course_id' => $this->course->id,
            'title' => 'Tugas 1 Eksponen',
            'created_by' => $this->guru->id,
        ]);
    }

    public function test_siswa_can_submit_assignment_and_guru_can_grade(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'title' => 'Tugas Harian 1',
            'instructions' => 'Kerjakan soal 1-5',
            'due_date' => now()->addDays(2),
            'max_score' => 100,
            'is_published' => true,
            'created_by' => $this->guru->id,
        ]);

        // 1. Siswa submits assignment
        $submitResponse = $this->actingAs($this->siswa)->post(route('lms.assignments.submit', [$this->course->id, $assignment->id]), [
            'notes' => 'Pak, ini tugas nomor 1 sampai 5 saya.',
        ]);

        $submitResponse->assertRedirect(route('lms.assignments.show', [$this->course->id, $assignment->id]));
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $this->siswa->id,
            'notes' => 'Pak, ini tugas nomor 1 sampai 5 saya.',
            'status' => 'submitted',
        ]);

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', $this->siswa->id)->first();

        // 2. Guru grades assignment
        $gradeResponse = $this->actingAs($this->guru)->post(route('lms.assignments.grade', [$this->course->id, $assignment->id, $submission->id]), [
            'score' => 90,
            'feedback' => 'Bagus sekali Ahmad, teruskan prestasimu!',
        ]);

        $gradeResponse->assertSessionHas('success');
        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'score' => 90,
            'feedback' => 'Bagus sekali Ahmad, teruskan prestasimu!',
            'status' => 'graded',
            'graded_by' => $this->guru->id,
        ]);
    }

    public function test_discussion_creation_and_replies(): void
    {
        // Siswa posts discussion topic
        $this->actingAs($this->siswa)->post(route('lms.discussions.store', $this->course->id), [
            'title' => 'Tanya soal nomor 2',
            'content' => 'Bagaimana cara memecahkan eksponen pecahan?',
        ]);

        $this->assertDatabaseHas('course_discussions', [
            'course_id' => $this->course->id,
            'user_id' => $this->siswa->id,
            'title' => 'Tanya soal nomor 2',
        ]);

        $disc = CourseDiscussion::where('course_id', $this->course->id)->first();

        // Guru replies
        $this->actingAs($this->guru)->post(route('lms.discussions.reply', [$this->course->id, $disc->id]), [
            'content' => 'Eksponen pecahan dapat diubah ke dalam bentuk akar.',
        ]);

        $this->assertDatabaseHas('discussion_replies', [
            'discussion_id' => $disc->id,
            'user_id' => $this->guru->id,
            'content' => 'Eksponen pecahan dapat diubah ke dalam bentuk akar.',
        ]);
    }
}
