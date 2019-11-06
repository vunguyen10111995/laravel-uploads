<?php

namespace App\Components\Upload;

interface UploadHandler
{
    public function store();

    public function delete($file);

    public function url($file);

    public function withFile($file);

    public function withUser($user);

    public function getFilePath();
}
