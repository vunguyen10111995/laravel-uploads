<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ClientFileUpload
{
    protected $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function hashedName()
    {
        return Uuid::uuid4()->toString();
    }

    //get name without extension

    public function originName()
    {
        return pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    public function type()
    {
        $mime = $this->mime();

        return !empty($mime)
            ? Str::plural(head(explode('/', $mime)))
            : 'unknown';
    }

    public function size()
    {
        return $this->file->getSize();
    }

    public function mime()
    {
        return $this->file->getClientMimeType();
    }

    public function extension()
    {
        return $this->file->getClientOriginalExtension();
    }

    public function toAttribute()
    {
        return [
            'hashed_name' => $this->hashedName(),
            'name' => $this->originName(),
            'type' => $this->type(),
            'size' => $this->size(),
            'mime' => $this->mime(),
            'extension' => $this->extension(),
        ];
    }
}
