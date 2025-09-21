<?php
namespace App\Trait;
trait HasImage
{
    public static $defaultImage = "/images/default-logo.png";
    public function uploadImage($image, $name)
    {
        if (!$image)
            return;

        $this->addMedia($image)
            ->usingName($name)
            ->toMediaCollection($name);
    }

    // Get image URL easily
    public function getImageUrl($name, $conversion = '')
    {
        $media = $this->getMedia($name)->last();
        if ($media) {
            return $media->getUrl();
        } else {
            return asset(self::$defaultImage);
        }
    }
}