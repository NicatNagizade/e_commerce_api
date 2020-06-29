<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class FileHelper
{
    private $file;
    private $file_name;
    private $has_file;
    private $file_path;

    public function __construct($file)
    {
        if (!$file) {
            $this->has_file = false;
            $this->file_name = null;
            return;
        }
        $this->has_file = true;
        $extension = $file->getClientOriginalExtension();
        $filename = time();
        $filename .= Str::random(20);
        $filename .= '.' . $extension;
        $this->file = $file;
        $this->file_name = $filename;
    }
    public function getFile()
    {
        return $this->file;
    }
    public function getName()
    {
        return $this->file_name;
    }
    public function save($public_path_folder): void
    {
        if ($this->has()) {
            $public_path_folder = trim($public_path_folder, '/');
            $path = public_path($public_path_folder);
            $this->file_path = $path . '/' . $this->getName();
            $this->file->move($path, $this->getName());
        }
    }
    public function has(): bool
    {
        return $this->has_file;
    }
    public function image(): \Intervention\Image\Image
    {
        return Image::make($this->file_path);
    }
}
