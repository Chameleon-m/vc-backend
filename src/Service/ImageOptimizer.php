<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageOptimizer
{
    private const MAX_WIDTH = 150;
    private const MAX_HEIGHT = 200;

    private Imagine $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @param string $filename
     * @return void
     * @throws \Imagine\Exception\RuntimeException
     */
    public function resize(string $filename): void
    {
        $info = new \SplFileInfo($filename);
//        $info->setInfoClass();
// RuntimeException
//        $groupID = $info->getGroup();
//        $ownerID = $info->getOwner();
//        if ($groupID === false) {
//            return;// todo log ex
//        }
//        $grgid = posix_getgrgid($groupID);
//        if ($grgid' === false || group_name' !== $grgid['name'] || !in_array('user_name', $grgid['members'])) {
//            return;// todo log ex
//        }
//        posix_getpwuid($ownerID)
//        $info->getPerms()

        if (!$info->isFile()){
            return;// todo log ex
        } else if (!$info->isReadable()){
            return;// todo log ex
        } else if (!$info->isWritable()) {
            return;// todo log ex
        }

        $size = getimagesize($filename);
        if ($size === false) {
            return;// todo log ex
        }

        [$iwidth, $iheight] = $size;
        $ratio = $iwidth / $iheight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo
            ->resize(new Box($width, $height))
            ->save($filename);
    }
}