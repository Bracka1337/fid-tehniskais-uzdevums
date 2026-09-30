<?php

namespace App\Services;

use App\Enums\AccessCategory;
use App\Enums\ImportanceLevel;
use App\Models\Document;
use DOMDocument;
use DOMElement;
use RuntimeException;

class DocumentXmlImporter
{
    public function import(string $xml, ?bool $validateSchema = null): int
    {
        $validateSchema ??= (bool) config('documents.validate_schema', true);

        $dom = new DOMDocument();

        if (@$dom->loadXML($xml) !== true) {
            throw new RuntimeException('Unable to parse documents XML.');
        }

        if ($validateSchema) {
            $xsd = resource_path('xsd/documents.xsd');

            if (! @$dom->schemaValidate($xsd)) {
                throw new RuntimeException('Documents XML failed XSD validation.');
            }
        }

        $imported = 0;

        foreach ($dom->getElementsByTagName('document') as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $data = $this->mapDocument($node);

            if ($data === null) {
                continue;
            }

            if (! $validateSchema) {
                if (ImportanceLevel::tryFrom($data['importance']) === null) {
                    continue;
                }

                if (AccessCategory::tryFrom($data['category']) === null) {
                    continue;
                }
            }

            Document::query()->updateOrCreate(
                ['external_id' => $data['external_id']],
                $data
            );

            $imported++;
        }

        return $imported;
    }

    private function mapDocument(DOMElement $node): ?array
    {
        $externalId = $this->childValue($node, 'external_id');
        $title = $this->childValue($node, 'title');

        if ($externalId === '' || $title === '') {
            return null;
        }

        return [
            'external_id' => $externalId,
            'title' => $title,
            'description' => $this->childValue($node, 'description'),
            'responsible_unit' => $this->childValue($node, 'responsible_unit'),
            'created_on' => $this->childValue($node, 'created_on'),
            'url' => $this->childValue($node, 'url'),
            'file_type' => $this->childValue($node, 'file_type'),
            'reading_time_minutes' => (int) $this->childValue($node, 'reading_time_minutes'),
            'importance' => $this->childValue($node, 'importance'),
            'category' => $this->childValue($node, 'category'),
            'is_active' => $this->childValue($node, 'is_active') === 'true',
        ];
    }

    private function childValue(DOMElement $node, string $name): string
    {
        $children = $node->getElementsByTagName($name);

        if ($children->length === 0 || $children->item(0) === null) {
            return '';
        }

        return trim($children->item(0)->textContent);
    }
}
