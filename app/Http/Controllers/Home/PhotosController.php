<?php


namespace App\Http\Controllers\Home;
use App\Models\Photo;


class PhotosController extends HomeAppController
{

    /**
     * View photo
     * @param int $alias
     * @return int|string
     */
    public function getView($alias = -1) {
       
        $photo = Photo::whereAlias($alias)->first();

        if (empty ($photo)) {
            return 'Image not found';
        }

        $imageFilePath = public_path($photo->photo_url);

        if (!file_exists($imageFilePath)) {
            return 'Image not found or deleted';
        }

        try {
            $decryptedImage = \Crypt::decrypt(file_get_contents($imageFilePath));

           switch (strtolower($photo->file_type)) {
                case 'jpg':
                    header("Content-Type: image/jpg");
                    break;
                case 'gif':
                    header("Content-Type: image/gif");
                    break;
                case 'png':
                    header("Content-Type: image/png");
                    break;
                default:
                    header("Content-Type: image/jpg");
                    break;
            }

            // Write the image bytes to the client
            echo $decryptedImage;
            die;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }
}