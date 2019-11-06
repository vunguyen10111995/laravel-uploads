<?php

namespace App\Components\Upload;

use App\Events\UploadProcessing;
use App\Events\UploadProcessed;
use Illuminate\Support\Manager;

class UploadManager extends Manager
{
    public function before($callback)
    {
        $this->container->make('events')->listen(UploadProcessing::class, $callback);
    }

    public function after($callback)
    {
        $this->container->make('events')->listen(UploadProcessed::class, $callback);
    }

    public function cycle($callback)
    {
        $this->container->make('events')->listen([UploadProcessing::class, UploadProcessed::class], $callback);
    }

    public function handler($driver = null)
    {
        return $this->driver($driver);
    }

    public function getDefaultDriver()
    {
        return 'avatar';
    }
}
