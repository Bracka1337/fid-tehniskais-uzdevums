<?php

namespace App\Console\Commands;

use App\Services\DocumentXmlImporter;
use App\Services\RemoteXmlClient;
use Illuminate\Console\Command;

class SyncDocumentsCommand extends Command
{
    protected $signature = 'documents:sync {--local : Read local XML instead of remote URL}';

    protected $description = 'Sync documents from remote or local XML into SQLite';

    public function handle(RemoteXmlClient $client, DocumentXmlImporter $importer): int
    {
        $xml = $client->fetch((bool) $this->option('local'));
        $count = $importer->import($xml);

        $this->info(sprintf('Synced %d document(s).', $count));

        return self::SUCCESS;
    }
}
