<?php

namespace App\Components\Upload;

use App\Models\Upload;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use RuntimeException;

abstract class Uploader
{
    protected $app;

    protected $file;

    protected $user;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function store()
    {
        $this->ensureValidFields();

        $this->dispatchUploadJob();
    }

    public function delete($file)
    {
        return $this->getDiskInstance()->delete($this->getStoredFilePath($file));
    }

    public function url($file)
    {
        return $this->getDiskInstance()->url($file);
    }

    public function withFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function withUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getFilePath()
    {
        throw new RuntimeException('Relative path to the file
                                   (with respect to the disk root directory and excluding the filename)
                                   must be specified.');
    }

    protected function ensureValidFields()
    {
        if (!($this->file && $this->file instanceof UploadedFile && $this->file->isValid())) {
            throw new RuntimeException('Invalid upload file');
        }

        if (!($this->user && $this->user instanceof Authenticatable)) {
            throw new RuntimeException('Cannot process the provided user');
        }
    }

    protected function dispatchUploadJob()
    {
        ProcessUpload::dispatch(
            $this->file,
            $this->getFilePath(),
            $this->user,
            $this->getDiskInstance(),
            $this->eventContext()
        );
    }

    protected function getStoredFilePath($file)
    {
        if ($file instanceof Upload) {
            $file = $file->filePath;
        }

        if (!is_string($file)) {
            throw new RuntimeException('Invalid file path');
        }

        return $file;
    }

    protected function getDiskInstance()
    {
        return $this->app->make('filesystem')->disk($this->getDiskName());
    }

    protected function getDiskName()
    {
        return $this->getCustomDiskName() ?? config('filesystems.default');
    }

    protected function getCustomDiskName()
    {
        return null;
    }

    final protected function eventContext()
    {
        return array_merge($this->extraEventContext(), [
            'handler' => $this,
        ]);
    }

    protected function extraEventContext()
    {
        return [];
    }
}
