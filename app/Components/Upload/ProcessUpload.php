<?php


namespace App\Components\Upload;

use App\Models\Upload;
use App\Support\ClientFileUpload;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use App\Events\UploadProcessed;
use App\Events\UploadProcessing;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class ProcessUpload
{
    use Dispatchable, SerializesModels;

    protected $user;

    protected $file;

    protected $path;

    protected $disk;

    protected $context;

    public function __construct(UploadedFile $file, $path, Authenticatable $user, Filesystem $disk, $context)
    {
        $this->file = $file;

        $this->path = $path;

        $this->user = $user;

        $this->disk = $disk;

        $this->context = $context;
    }

    public function handle()
    {
        DB::transaction(function () {
            UploadProcessing::dispatch($this->file, $this->user, $this->context);

            $upload = $this->storeUploadFile();

            UploadProcessed::dispatch($this->user, $upload, $this->context);

            return $upload;
        });
    }

    protected function storeUploadFile()
    {
        $upload = Upload::create(
            array_merge(
                ['owner_id' => $this->user->id, 'path' => $this->path],
                (new ClientFileUpload($this->file))->toAttribute()
            )
        );

        $this->disk->putFileAs($upload->path, $this->file, $upload->basename);

        return $upload;
    }
}
