<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonalizedPicService
{
    private string $uploadDir;

    public function __construct(string $uploadClientPhotoDir)
    {
        $this->uploadDir = rtrim($uploadClientPhotoDir, '/');
    }

    public function getPersonalizedPic(string $filename): BinaryFileResponse
    {
        $path = $this->uploadDir . '/' . $filename;

        if (!file_exists($path)) {
          throw new NotFoundHttpException('Image introuvable');
        }

        return new BinaryFileResponse($path, 200, [], false, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}