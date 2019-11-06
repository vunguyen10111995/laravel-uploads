<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadProcessed
{
    use Dispatchable, SerializesModels;

    public $user;

    public $upload;

    public $context;

    public function __construct(Authenticatable $user, Upload $upload, $context = [])
    {
        $this->user = $user;

        $this->upload = $upload;

        $this->context = $context;
    }
}
