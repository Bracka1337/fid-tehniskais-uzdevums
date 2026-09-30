<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;

class WipeDocumentsCommand extends Command
{
    protected $signature = 'documents:wipe';

    protected $description = 'Delete all document rows';

    public function handle(): int
    {
        $deleted = Document::query()->delete();

        $this->info(sprintf('Deleted %d document row(s).', $deleted));

        return self::SUCCESS;
    }
}
