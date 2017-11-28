<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Photo extends AppModel
{
    public $fillable = [
        'order_id', 'photo_url', 'file_size', 'file_type'
    ];

    /**
     * Encrypt image to encrypted image
     */
    public function toEncryptImage() {
        if (strpos($this->photo_url, $this->alias) !== false) {
            return false;
        }

        $path = public_path($this->photo_url);

        if (!file_exists($path)) {
            return false;
        }

        $pathInfo = pathinfo($path);
        $urlInfo = pathinfo($this->photo_url);

        $dirName = $pathInfo['dirname'];

        $encryptedImage = \Crypt::encrypt(file_get_contents($path));
        $newFile = fopen($dirName . '/' . $this->alias, 'w');
        fwrite($newFile, $encryptedImage);
        fclose($newFile);
        $this->photo_url = $urlInfo['dirname'] . '/' . $this->alias;
        @unlink($path);
        return $this->save();
    }
}