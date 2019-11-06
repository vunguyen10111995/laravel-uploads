<?php

namespace App\Components\Upload\Handlers;

use App\Components\Upload\Uploader;
use App\Components\Upload\UploadHandler;

class Avatar extends Uploader implements UploadHandler
{
    protected function getCustomDiskName()
    {
        return 'avatars';
    }

    public function getFilePath()
    {
        return (string) $this->user->id;
    }

    public function getDefaultAvatarUrl()
    {
        return $this->url('default.png');
    }
}
