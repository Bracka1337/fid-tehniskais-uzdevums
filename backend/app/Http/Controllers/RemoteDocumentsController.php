<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class RemoteDocumentsController extends Controller
{
    public function __invoke(): Response
    {
        $path = config('documents.local_path');

        abort_unless(is_string($path) && File::exists($path), 404);

        return response(File::get($path), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
