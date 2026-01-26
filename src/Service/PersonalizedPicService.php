<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonalizedPicService
{
    private string $uploadCartPhotoDir;
    private string $uploadUnvalidedOrderPhotoDir;
    private string $uploadValidedOrderPhotoDir;

    public function __construct(string $uploadClientPhotoDir, string $uploadUnvalidedOrderPhotoDir, string $uploadValidedOrderPhotoDir)
    {
        $this->uploadCartPhotoDir = rtrim($uploadClientPhotoDir, '/');
        $this->uploadUnvalidedOrderPhotoDir = rtrim($uploadUnvalidedOrderPhotoDir, '/');
        $this->uploadValidedOrderPhotoDir = rtrim($uploadValidedOrderPhotoDir, '/');
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

    public function getValidedOrderPhoto(string $filename): BinaryFileResponse
    {
        $path = $this->uploadValidedOrderPhotoDir . '/' . $filename;

        if (!file_exists($path)) {
          throw new NotFoundHttpException('Image introuvable');
        }

        return new BinaryFileResponse($path, 200, [], false, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}