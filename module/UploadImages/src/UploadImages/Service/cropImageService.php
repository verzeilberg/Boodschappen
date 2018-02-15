<?php

namespace UploadImages\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * Entities
 */
use UploadImages\Entity\ImageFile;

class cropImageService implements cropImageServiceInterface {

    /**
     * @var \Blog\Service\PostServiceInterface
     */
    protected $config;
    protected $em;

    public function __construct($em, $config) {
        $this->config = $config;
        $this->em = $em;
    }

    public function uploadImage($image, $imageUploadSettings = NULL) {

        //Check if provided image ia an array
        if (!is_array($image)) {
            return $this->translate('File is not a image');
        }

        $sUploadFolder = '';
        $sUploadFolder = 'public/' . $this->config['imageUploadSettings']['uploadFolder'];
        $iUploadeFileSize = 0;
        $iUploadeFileSize = (int) $this->config['imageUploadSettings']['uploadeFileSize'];
        $aAllowedFileTypes = array();
        $aAllowedFileTypes = $this->config['imageUploadSettings']['allowedImageTypes'];

        //Target directory with file name
        $target_file = $sUploadFolder . basename($image["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

        // Check if image file is a actual image or fake image
        $check = getimagesize($image["tmp_name"]);
        if ($check === false) {
            return $this->translate('File is not a image');
        }

        // Check if folder excists and has the apropiete rights otherwise create and give rights
        if (!file_exists($sUploadFolder)) {
            mkdir($sUploadFolder, 0777, true);
        } elseif (!is_writable($sUploadFolder)) {
            chmod($sUploadFolder, 0777);
        }
        // Check if image file already exists
        if (file_exists($target_file)) {
            return 'File already excist';
        }
        // Check image file size
        if ($image["size"] > $iUploadeFileSize) {
            return 'Image not saved. File size exceeded';
        }

        
        $imageFileType = strtolower($imageFileType);
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
        }

        //var_Dump($image["tmp_name"]); die;
        
        
        if (copy($image["tmp_name"], $target_file)) {

            $ImageFile = array();
            $ImageFile['imageFileName'] = $image["name"];
            $ImageFile['imageFolderName'] = $this->config['imageUploadSettings']['uploadFolder'];

            return $ImageFile;
        } else {
            return 'Sorry, there was an error uploading your file.';
        }
    }

    public function resizeAndCropImage($sOriLocation, $sDestinationFolder = null, $iImgWidth = null, $iImgHeight = null) {

        // Check if file exist
        if (!file_exists($sOriLocation)) {
            return 'File does not exist.';
        }

        // Check if the destionfolder is set. When false than Original location becomes Destination folder
        if ($sDestinationFolder == null) {
            $sDestinationFolder = dirname($sOriLocation);
        }

        // Check if the destination folder exist
        if (!is_dir($sDestinationFolder)) {
            return 'Folder ' . $sDestinationFolder . ' does not exist';
        }

        // Check if the directory has the appropiate rights
        if (substr(sprintf('%o', fileperms($sDestinationFolder)), -4) <> '0777') {
            return 'The folder does not has the appropirate rights to upload files.';
        }

        /*
          // Check is the file size is not to big Smaller than 50 mb
          // File size can be set in incl/config.php file
          if ($this->iFileSize  > MAX_FILE_SIZE) {
          $sStatusMessage = 'The file size is to big.';
          echo $sStatusMessage;
          exit;
          } */


        $sPathParts = pathinfo($sOriLocation);

        $sFileName = $sPathParts['basename'];

        $sMimeType = mime_content_type($sOriLocation);

        // Depending on wich file type is uploaded create a image
        if ($sMimeType == "image/jpeg") {
            $oSourceImage = imagecreatefromjpeg($sOriLocation);
        } else if ($sMimeType == "image/png") {
            $oSourceImage = imagecreatefrompng($sOriLocation);
        } else if ($sMimeType == "image/gif") {
            $oSourceImage = imagecreatefromgif($sOriLocation);
        } else {
            return 'The file is not a image';
        }

        // Get the widht and height of the uploade image        
        $aFileProps = getimagesize($sOriLocation);

        $iWidth = $aFileProps[0];
        $iHeight = $aFileProps[1];

        $original_aspect = $iWidth / $iHeight;
        $thumb_aspect = $iImgWidth / $iImgHeight;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $iImgHeight;
            $new_width = $iWidth / ($iHeight / $iImgHeight);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $iImgWidth;
            $new_height = $iHeight / ($iWidth / $iImgWidth);
        }

        # Create Temporary image with new Width and height
        # iNewWidth -> integer
        # iNewHeight -> integer
        $oTempImage = imagecreatetruecolor($iImgWidth, $iImgHeight);


        if ($sMimeType == "image/png" || $sMimeType == "image/gif") {

            $oTransparentIndex = imagecolortransparent($oSourceImage);
            if ($oTransparentIndex >= 0) { // GIF
                imagepalettecopy($oSourceImage, $oTempImage);
                imagefill($oTempImage, 0, 0, $oTransparentIndex);
                imagecolortransparent($oTempImage, $oTransparentIndex);
                imagetruecolortopalette($oTempImage, true, 256);
            } else { // PNG
                imagealphablending($oTempImage, false);
                imagesavealpha($oTempImage, true);
                $oTransparent = imagecolorallocatealpha($oTempImage, 255, 255, 255, 127);
                imagefilledrectangle($oTempImage, 0, 0, $iImgWidth, $iImgHeight, $oTransparent);
            }
        }

        // Resize and crop
        imagecopyresampled($oTempImage, $oSourceImage, 0 - ($new_width - $iImgWidth) / 2, 0 - ($new_height - $iImgHeight) / 2, 0, 0, $new_width, $new_height, $iWidth, $iHeight);

        $sPathToFile = $sDestinationFolder . '/' . $sFileName;

        //Check MimeType to create image
        if ($sMimeType == "image/jpeg") {
            imagejpeg($oTempImage, $sPathToFile, 80);
        } else if ($sMimeType == "image/png") {
            imagepng($oTempImage, $sPathToFile, 9);
        } else if ($sMimeType == "image/gif") {
            imagegif($oTempImage, $sPathToFile);
        } else {
            return 'Image could not be resized';
        }
        imagedestroy($oSourceImage);
        imagedestroy($oTempImage);

        $ImageFile = array();
        $ImageFile['imageFileName'] = $sFileName;
        $ImageFile['imageFolderName'] = $sDestinationFolder;

        return $ImageFile;
    }

    public function CropImage($srcFile = null, $dstFile = null, $x, $y, $w, $h, $dw, $dh, $img_quality = 90) {

        // Check if file exist
        if (!file_exists($srcFile)) {
            return 'Could not find the original image';
        }

        // Check if the destionfolder is set. When false than Original location becomes Destination folder
        if ($dstFile == null) {
            $dstFile = dirname($srcFile);
        }

        // Check if the destination folder exist
        if (!is_dir($dstFile)) {
            return 'Folder ' . $dstFile . ' does not exist';
        }

        // Check if the directory has the appropiate rights
        if (substr(sprintf('%o', fileperms($dstFile)), -4) <> '0777') {
            return 'The folder does not has the appropirate rights to upload files.';
        }

        //get file info like basename and mime type of the file
        $sPathParts = pathinfo($srcFile);
        $sFileName = $sPathParts['basename'];
        $sMimeType = mime_content_type($srcFile);

        // Switch between jpg, png or gif
        switch ($sMimeType) {
            case "image/jpeg":
                $img_r = imagecreatefromjpeg($srcFile);
                $dst_r = ImageCreateTrueColor($dw, $dh);
                imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $dw, $dh, $w, $h);
                imagejpeg($dst_r, $dstFile . $sFileName, $img_quality);
                break;
            case "image/png":
                $img_r = imagecreatefrompng($srcFile);
                $dst_r = ImageCreateTrueColor($dw, $dh);

                $oTransparentIndex = imagecolortransparent($srcFile);
                imagealphablending($dst_r, false);
                imagesavealpha($dst_r, true);
                $oTransparent = imagecolorallocatealpha($dst_r, 255, 255, 255, 127);
                imagefilledrectangle($dst_r, 0, 0, $dw, $dh, $oTransparent);


                imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $dw, $dh, $w, $h);
                imagepng($dst_r, $dstFile . $sFileName, 9);
                break;
            case "image/gif":
                $img_r = imagecreatefromgif($srcFile);
                $dst_r = ImageCreateTrueColor($dw, $dh);

                $oTransparentIndex = imagecolortransparent($srcFile);
                imagepalettecopy($srcFile, $dst_r);
                imagefill($dst_r, 0, 0, $oTransparentIndex);
                imagecolortransparent($dst_r, $oTransparentIndex);
                imagetruecolortopalette($dst_r, true, 256);

                imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $dw, $dh, $w, $h);
                imagegif($dst_r, $dstFile . $sFileName);
                break;
        }
        
        return true;
    }

    public function setImageUploadSettings($imageUploadSettings) {
        if ($imageUploadSettings === NULL) {
            $this->imageUploadSettings = $this->getServiceLocator()->get('config');
        } else {

            $formatArray = array(
                'uploadFolder' => 'public/img/userFiles/',
                'uploadeFileSize' => '5000000000000000',
                'allowedImageTypes' => array(
                    'jpg',
                    'png',
                    'gif'
                )
            );

            if (array_diff($formatArray, $imageUploadSettings)) {
                return false;
            }

            $this->imageUploadSettings = $imageUploadSettings;
        }
    }

}
