<?php


namespace App\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UploadProcessing
{
    use Dispatchable, SerializesModels;

    public $file;

    public $user;

    public $context = [];

    public function __construct(UploadedFile $file, Authenticatable $user, $context = [])
    {
        $this->file = $file;

        $this->user = $user;

        $this->context = $context;
    }
}
