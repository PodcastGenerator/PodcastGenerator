<?php  declare(strict_types=1);

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

namespace PodcastGenerator\Models\Admin;

use DateTime;
use Exception;

trait CoverImageTrait
{
    abstract public function getCoverImageUrl(): ?string;

    abstract public function setCoverImage(string $path);

    abstract protected function addValidationError(string $field, string $error);

    public function saveCoverImageFile($fileData): string|false
    {
        $coverImage = str_replace(' ', '_', basename($fileData['name']));
        $coverImageExt = pathinfo($coverImage, PATHINFO_EXTENSION);
        $imagesDir = self::$config['absoluteurl'] . self::$config['img_dir'];

        $validExtensions = getSupportedFileExtensions(self::$config, ['image']);
        $validCoverFileExt = in_array($coverImageExt, $validExtensions);
        if (!$validCoverFileExt) {
            $this->addValidationError('cover', sprintf(_('%s has invalid file extension'), $coverImage));
            return false;
        }

        $coverImageFile = makeUniqueFilename($imagesDir . $coverImage);
        if (!move_uploaded_file($fileData['tmp_name'], $coverImageFile)) {
            $this->addValidationError('', sprintf(_('%s was not uploaded successfully'), $coverImage));
            return false;
        }

        $coverMimeType = getmime($coverImageFile);
        if (!$coverMimeType) {
            $this->addValidationError('', _('The uploaded cover art file is not readable (permission error)'));
            return false;
        }

        $validMimeTypes = getSupportedMimeTypes(self::$config, ['image']);
        $validCoverMimeType = in_array($coverMimeType, $validMimeTypes);
        if (!$validCoverMimeType) {
            $this->addValidationError(
                'cover',
                sprintf(_('%s has unsupported MIME content type %s'), $coverImage, $coverMimeType)
            );
            // Delete the file if the mime type is invalid
            unlink($coverImageFile);
            return false;
        }

        $this->setCoverImage($coverImageFile);
        return $coverImageFile;
    }
}
