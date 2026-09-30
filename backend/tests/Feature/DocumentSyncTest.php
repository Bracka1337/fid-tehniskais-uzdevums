<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Services\DocumentXmlImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DocumentSyncTest extends TestCase
{
    use RefreshDatabase;

    private function placeFixture(?string $source = null): string
    {
        $source ??= base_path('tests/Fixtures/documents.xml');
        $destination = storage_path('app/remote/documents.xml');

        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);

        return $destination;
    }

    public function test_remote_documents_endpoint_returns_xml(): void
    {
        $this->placeFixture();

        $response = $this->get('/remote/documents.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<external_id>DOC-TEST-001</external_id>', false);
    }

    public function test_sync_local_imports_documents_and_is_idempotent(): void
    {
        $this->placeFixture();

        $this->artisan('documents:sync', ['--local' => true])
            ->assertSuccessful();

        $this->assertSame(3, Document::query()->count());
        $this->assertTrue(Document::query()->where('external_id', 'DOC-TEST-001')->exists());

        $this->artisan('documents:sync', ['--local' => true])
            ->assertSuccessful();

        $this->assertSame(3, Document::query()->count());
        $this->assertSame(3, Document::query()->distinct('external_id')->count('external_id'));
    }

    public function test_sync_falls_back_to_local_when_remote_unavailable(): void
    {
        $this->placeFixture();

        Http::fake([
            'http://remote.test/*' => Http::failedConnection(),
        ]);

        config(['documents.remote_url' => 'http://remote.test/documents.xml']);

        $this->artisan('documents:sync')
            ->assertSuccessful();

        $this->assertSame(3, Document::query()->count());
    }

    public function test_invalid_importance_is_skipped_when_schema_validation_disabled(): void
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<documents>
  <document>
    <external_id>DOC-BAD-001</external_id>
    <title>Bad Importance</title>
    <description>Should be skipped</description>
    <responsible_unit>IT</responsible_unit>
    <created_on>2024-01-01</created_on>
    <url>https://example.com/bad.pdf</url>
    <file_type>pdf</file_type>
    <reading_time_minutes>10</reading_time_minutes>
    <importance>urgent</importance>
    <category>public</category>
    <is_active>true</is_active>
  </document>
  <document>
    <external_id>DOC-GOOD-001</external_id>
    <title>Good Document</title>
    <description>Should import</description>
    <responsible_unit>IT</responsible_unit>
    <created_on>2024-01-02</created_on>
    <url>https://example.com/good.pdf</url>
    <file_type>pdf</file_type>
    <reading_time_minutes>15</reading_time_minutes>
    <importance>low</importance>
    <category>public</category>
    <is_active>true</is_active>
  </document>
</documents>
XML;

        /** @var DocumentXmlImporter $importer */
        $importer = $this->app->make(DocumentXmlImporter::class);

        $imported = $importer->import($xml, false);

        $this->assertSame(1, $imported);
        $this->assertSame(1, Document::query()->count());
        $this->assertTrue(Document::query()->where('external_id', 'DOC-GOOD-001')->exists());
        $this->assertFalse(Document::query()->where('external_id', 'DOC-BAD-001')->exists());
    }
}
