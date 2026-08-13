<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Role;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SoftDeleteAndFileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_upload_service_uploads_different_file_types_to_category_folders(): void
    {
        $fakeImage = UploadedFile::fake()->create('avatar.jpg', 10, 'image/jpeg');
        $fakePdf = UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf');
        $fakeVideo = UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4');

        $imagePath = upload_file($fakeImage, 'profile', null, 'test_avatar');
        $pdfPath = upload_file($fakePdf, 'documents', null, 'test_pdf');
        $videoPath = upload_file($fakeVideo, 'videos', null, 'test_video');

        $this->assertStringStartsWith('uploads/profile/', $imagePath);
        $this->assertStringStartsWith('uploads/documents/', $pdfPath);
        $this->assertStringStartsWith('uploads/videos/', $videoPath);

        $this->assertTrue(File::exists(public_path($imagePath)));
        $this->assertTrue(File::exists(public_path($pdfPath)));
        $this->assertTrue(File::exists(public_path($videoPath)));

        // Clean up
        delete_file($imagePath);
        delete_file($pdfPath);
        delete_file($videoPath);

        $this->assertFalse(File::exists(public_path($imagePath)));
        $this->assertFalse(File::exists(public_path($pdfPath)));
        $this->assertFalse(File::exists(public_path($videoPath)));
    }

    public function test_file_upload_service_deletes_old_file_when_replacing(): void
    {
        $firstFile = UploadedFile::fake()->create('old_logo.png', 10, 'image/png');
        $firstPath = FileUploadService::upload($firstFile, 'settings', null, 'logo');

        $this->assertTrue(File::exists(public_path($firstPath)));

        $secondFile = UploadedFile::fake()->create('new_logo.png', 10, 'image/png');
        $secondPath = FileUploadService::upload($secondFile, 'settings', $firstPath, 'logo');

        $this->assertFalse(File::exists(public_path($firstPath)));
        $this->assertTrue(File::exists(public_path($secondPath)));

        // Clean up
        FileUploadService::delete($secondPath);
    }

    public function test_customer_soft_delete(): void
    {
        $customer = Customer::create([
            'customer_type' => 'user',
            'name' => 'Soft Delete Customer',
            'mobile' => '9999900000',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('customers', ['customer_id' => $customer->customer_id, 'deleted_at' => null]);

        $customer->delete();

        $this->assertSoftDeleted('customers', ['customer_id' => $customer->customer_id]);
        $this->assertCount(0, Customer::where('customer_id', $customer->customer_id)->get());
        $this->assertCount(1, Customer::onlyTrashed()->where('customer_id', $customer->customer_id)->get());
    }

    public function test_lead_soft_delete(): void
    {
        $customer = Customer::create([
            'customer_type' => 'user',
            'name' => 'Lead Customer',
            'mobile' => '9999911111',
            'status' => 1,
        ]);

        $lead = Lead::create([
            'customer_id' => $customer->customer_id,
            'lead_title' => 'Sample Lead',
            'priority' => 'medium',
            'status' => 1,
        ]);

        $lead->delete();

        $this->assertSoftDeleted('leads', ['lead_id' => $lead->lead_id]);
        $this->assertCount(0, Lead::where('lead_id', $lead->lead_id)->get());
        $this->assertCount(1, Lead::onlyTrashed()->where('lead_id', $lead->lead_id)->get());
    }

    public function test_lead_source_soft_delete(): void
    {
        $source = LeadSource::create([
            'name' => 'Facebook Ads',
            'status' => 1,
        ]);

        $source->delete();

        $this->assertSoftDeleted('lead_sources', ['lead_sources_id' => $source->lead_sources_id]);
        $this->assertCount(0, LeadSource::where('lead_sources_id', $source->lead_sources_id)->get());
        $this->assertCount(1, LeadSource::onlyTrashed()->where('lead_sources_id', $source->lead_sources_id)->get());
    }

    public function test_user_soft_delete(): void
    {
        $user = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
        ]);

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertCount(0, User::where('id', $user->id)->get());
        $this->assertCount(1, User::onlyTrashed()->where('id', $user->id)->get());
    }

    public function test_role_soft_delete(): void
    {
        $role = Role::create([
            'name' => 'Custom Role',
            'guard_name' => 'web',
        ]);

        $role->delete();

        $this->assertSoftDeleted('roles', ['id' => $role->id]);
        $this->assertCount(0, Role::where('id', $role->id)->get());
        $this->assertCount(1, Role::onlyTrashed()->where('id', $role->id)->get());
    }
}
