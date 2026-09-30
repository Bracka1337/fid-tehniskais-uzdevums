<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RemoteXmlClient
{
    public function fetch(bool $localOnly = false): string
    {
        $localPath = config('documents.local_path');

        if ($localOnly) {
            return $this->readLocal($localPath);
        }

        $url = (string) config('documents.remote_url');

        try {
            $response = Http::timeout(10)->accept('application/xml')->get($url);

            if ($response->successful() && filled($response->body())) {
                return $response->body();
            }
        } catch (\Throwable) {
        }

        return $this->readLocal($localPath);
    }

    private function readLocal(string $path): string
    {
        if (! File::exists($path)) {
            throw new RuntimeException("Local documents XML not found at {$path}");
        }

        return File::get($path);
    }
}
