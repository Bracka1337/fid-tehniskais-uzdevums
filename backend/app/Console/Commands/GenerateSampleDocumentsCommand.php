<?php

namespace App\Console\Commands;

use App\Enums\AccessCategory;
use App\Enums\ImportanceLevel;
use DOMDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSampleDocumentsCommand extends Command
{
    protected $signature = 'documents:generate-sample {--count=25 : Number of sample documents}';

    protected $description = 'Generate sample remote documents XML';

    public function handle(): int
    {
        $count = max(1, (int) $this->option('count'));

        $titles = [
            'Iekšējās kontroles politika',
            'Datu aizsardzības vadlīnijas',
            'Informācijas drošības noteikumi',
            'Projekta vadības rokasgrāmata',
            'Budžeta plānošanas kārtība',
            'Personāla atlases procedūra',
            'Risku pārvaldības ietvars',
            'Kvalitātes nodrošināšanas standarts',
            'Klīnisko datu apstrādes instrukcija',
            'Publisko iepirkumu kārtība',
            'Dokumentu aprites noteikumi',
            'Incidentu reaģēšanas plāns',
            'IT infrastruktūras politika',
            'Komunikācijas vadlīnijas',
            'Auditēšanas metodika',
        ];

        $units = [
            'IT departaments',
            'Juridiskais dienests',
            'Finanšu nodaļa',
            'Personāla daļa',
            'Kvalitātes vadība',
            'Drošības dienests',
            'Stratēģiskā plānošana',
        ];

        $descriptions = [
            'Dokuments apraksta procesa prasības un atbildības.',
            'Šis dokuments nosaka pamatprincipus un piemērošanas kārtību.',
            'Saturs paredzēts darbinieku ikdienas darbam un kontrolei.',
            'Vadlīnijas izmantojamas plānošanā un uzraudzībā.',
            'Dokuments apkopo labās prakses piemērus un minimālās prasības.',
        ];

        $fileTypes = ['pdf', 'docx', 'xlsx', 'odt', 'html'];

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('documents');
        $dom->appendChild($root);

        for ($i = 1; $i <= $count; $i++) {
            $document = $dom->createElement('document');

            $createdOn = (new \DateTimeImmutable(sprintf('-%d days', random_int(0, 730))))->format('Y-m-d');
            $fileType = $fileTypes[array_rand($fileTypes)];
            $title = $titles[array_rand($titles)].' #'.$i;

            $fields = [
                'external_id' => sprintf('DOC-%04d', $i),
                'title' => $title,
                'description' => $descriptions[array_rand($descriptions)],
                'responsible_unit' => $units[array_rand($units)],
                'created_on' => $createdOn,
                'url' => sprintf('https://example.com/documents/%04d.%s', $i, $fileType),
                'file_type' => $fileType,
                'reading_time_minutes' => (string) random_int(5, 90),
                'importance' => ImportanceLevel::cases()[array_rand(ImportanceLevel::cases())]->value,
                'category' => AccessCategory::cases()[array_rand(AccessCategory::cases())]->value,
                'is_active' => random_int(0, 100) < 85 ? 'true' : 'false',
            ];

            foreach ($fields as $name => $value) {
                $element = $dom->createElement($name);
                $element->appendChild($dom->createTextNode($value));
                $document->appendChild($element);
            }

            $root->appendChild($document);
        }

        $directory = storage_path('app/remote');
        File::ensureDirectoryExists($directory);

        $path = $directory.DIRECTORY_SEPARATOR.'documents.xml';
        $dom->save($path);

        $this->info(sprintf('Generated %d sample documents at %s', $count, $path));

        return self::SUCCESS;
    }
}
