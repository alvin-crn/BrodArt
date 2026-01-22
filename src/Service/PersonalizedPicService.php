<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonalizedPicService
{
    private string $uploadCartPhotoDir;
    private string $uploadUnvalidedOrderPhotoDir;

    public function __construct(string $uploadClientPhotoDir, string $uploadUnvalidedOrderPhotoDir)
    {
        $this->uploadCartPhotoDir = rtrim($uploadClientPhotoDir, '/');
        $this->uploadUnvalidedOrderPhotoDir = rtrim($uploadUnvalidedOrderPhotoDir, '/');
    }

    public function getPersonalizedPic(string $filename): BinaryFileResponse
    {
        $path = $this->uploadCartPhotoDir . '/' . $filename;

        if (!file_exists($path)) {
          throw new NotFoundHttpException('Image introuvable');
        }

        return new BinaryFileResponse($path, 200, [], false, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    public function getUnvalidedOrderPhoto(string $filename): BinaryFileResponse
    {
        $path = $this->uploadUnvalidedOrderPhotoDir . '/' . $filename;

        if (!file_exists($path)) {
          throw new NotFoundHttpException('Image introuvable');
        }

        return new BinaryFileResponse($path, 200, [], false, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}