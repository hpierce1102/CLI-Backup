<?php

namespace Backup\Uploader;

interface UploaderInterface
{
    public function publishFiles(Array $assocArray);
    public function publishFile(String $filePath, String $location);
    public function purgeFile(String $filePath);
    public function purgeFiles(Array $assocArray);
    public function listFiles(String $filePath);
}