<?php

namespace Tests\Feature;

use App\Enums\AccessCategory;
use App\Enums\ImportanceLevel;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentApiTest extends TestCase
{
    use RefreshDatabase;

    private function authUser(): User
    {
        return User::factory()->create();
    }

    private function seedDocuments(): void
    {
        Document::query()->create([
            'external_id' => 'DOC-A',
            'title' => 'Alpha Policy',
            'description' => 'Alpha description about security',
            'responsible_unit' => 'IT departaments',
            'created_on' => '2024-01-10',
            'url' => 'https://example.com/a.pdf',
            'file_type' => 'pdf',
            'reading_time_minutes' => 10,
            'importance' => ImportanceLevel::High,
            'category' => AccessCategory::Public,
            'is_active' => true,
        ]);

        Document::query()->create([
            'external_id' => 'DOC-B',
            'title' => 'Beta Guide',
            'description' => 'Beta internal notes',
            'responsible_unit' => 'Juridiskais dienests',
            'created_on' => '2024-06-15',
            'url' => 'https://example.com/b.docx',
            'file_type' => 'docx',
            'reading_time_minutes' => 20,
            'importance' => ImportanceLevel::Low,
            'category' => AccessCategory::Internal,
            'is_active' => true,
        ]);

        Document::query()->create([
            'external_id' => 'DOC-C',
            'title' => 'Charlie Manual',
            'description' => 'Charlie archived content',
            'responsible_unit' => 'Finanšu nodaļa',
            'created_on' => '2023-12-01',
            'url' => 'https://example.com/c.pdf',
            'file_type' => 'pdf',
            'reading_time_minutes' => 5,
            'importance' => ImportanceLevel::Critical,
            'category' => AccessCategory::Confidential,
            'is_active' => false,
        ]);
    }

    public function test_documents_can_be_filtered_by_importance_and_category(): void
    {
        $this->seedDocuments();

        $response = $this
            ->actingAs($this->authUser())
            ->getJson('/api/documents?importance=high&category=public');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.external_id', 'DOC-A');
    }

    public function test_documents_can_be_filtered_by_is_active(): void
    {
        $this->seedDocuments();

        $response = $this
            ->actingAs($this->authUser())
            ->getJson('/api/documents?is_active=0');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.external_id', 'DOC-C');
    }

    public function test_documents_can_be_searched(): void
    {
        $this->seedDocuments();

        $response = $this
            ->actingAs($this->authUser())
            ->getJson('/api/documents?search=security');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.title', 'Alpha Policy');
    }

    public function test_documents_can_be_sorted_by_title(): void
    {
        $this->seedDocuments();

        $asc = $this
            ->actingAs($this->authUser())
            ->getJson('/api/documents?sort=title&direction=asc');

        $asc->assertOk();
        $asc->assertJsonPath('data.0.title', 'Alpha Policy');
        $asc->assertJsonPath('data.2.title', 'Charlie Manual');

        $desc = $this
            ->actingAs($this->authUser())
            ->getJson('/api/documents?sort=title&direction=desc');

        $desc->assertOk();
        $desc->assertJsonPath('data.0.title', 'Charlie Manual');
        $desc->assertJsonPath('data.2.title', 'Alpha Policy');
    }

    public function test_documents_response_includes_meta_total(): void
    {
        $this->seedDocuments();

        $response = $this
            ->actingAs($this->authUser())
            ->getJson('/api/documents');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 3);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'external_id',
                    'title',
                    'description',
                    'responsible_unit',
                    'created_on',
                    'url',
                    'file_type',
                    'reading_time_minutes',
                    'importance',
                    'category',
                    'is_active',
                ],
            ],
            'meta' => ['total'],
        ]);
    }
}
