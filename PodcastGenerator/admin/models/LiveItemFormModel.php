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
use Lootils\Uuid\Uuid;
use PodcastGenerator\Configuration;

class LiveItemFormModel
{
    private const DATE_FORMAT = 'Y-m-d';
    private const TIME_FORMAT = 'H:i';

    private static Configuration $config;

    private string $name;

    public function name(): string
    {
        return $this->name;
    }

    public ?Uuid $guid;

    public string $title;

    public string $status;

    public string $shortDesc;
    public string $longDesc;

    private ?DateTime $startTime = null;
    public string $startTimeDate;
    public string $startTimeTime;

    private ?DateTime $endTime = null;
    public string $endTimeDate;
    public string $endTimeTime;

    public string $streamUrl;
    public string $streamType;

    public string $authorName;
    public string $authorEmail;

    public string $customTags;

    public ?string $coverImagePath = null;
    public ?string $coverImageUrl = null;
    private bool $hasNewCoverImage = false;

    private array $validationMessages = [];

    public static array $statusOptions;

    public static array $mimeTypeOptions;

    public static function initialize($config)
    {
        self::$statusOptions = [
            [ 'value' => LIVEITEM_STATUS_PENDING, 'label' => _('Pending') ],
            [ 'value' => LIVEITEM_STATUS_LIVE, 'label' => _('Live') ],
            [ 'value' => LIVEITEM_STATUS_ENDED, 'label' => _('Ended') ]
        ];

        $mimetypes = getSupportedMimeTypes($config, ['audio', 'video']);
        self::$mimeTypeOptions = [
            [ 'value' => '', 'label' => sprintf(_('Default (%s)'), $config['liveitems_default_mimetype']) ]
        ];
        foreach ($mimetypes as $mimetype) {
            self::$mimeTypeOptions[] = [ 'value' => $mimetype, 'label' => $mimetype ];
        }

        self::$config = $config;
    }

    private function __construct($name)
    {
        $this->name = $name;
    }

    public static function fromForm($GET, $POST): LiveItemFormModel
    {
        $model = new LiveItemFormModel($GET['name']);

        try {
            $model->guid = new Uuid('urn:uuid:' . $POST['guid']);
        } catch (Exception $e) {
            // ignore, we don't apply this anyway
        }

        $model->title = $POST['title'];

        $model->status = $POST['status'];

        $model->shortDesc = $POST['shortDesc'];
        $model->longDesc = $POST['longDesc'];

        $model->startTimeDate = $POST['startDate'];
        $model->startTimeTime = $POST['startTime'];

        $model->endTimeDate = $POST['endDate'];
        $model->endTimeTime = $POST['endTime'];

        $model->streamUrl = $POST['streamUrl'];
        $model->streamType = $POST['streamType'];

        $model->authorName = $POST['authorName'];
        $model->authorEmail = $POST['authorEmail'];

        $model->customTags = $POST['customtags'];

        $model->coverImageUrl = $POST['coverImageUrl'];

        try {
            $model->startTime = new DateTime($model->startTimeDate . ' ' . $model->startTimeTime);
        } catch (Exception $e) {
            $model->addValidationError('startTime', $e->getMessage());
            $model->startTime = null;
        }

        try {
            $model->endTime = new DateTime($model->endTimeDate . ' ' . $model->endTimeTime);
        } catch (Exception $e) {
            $model->addValidationError('endTime', $e->getMessage());
            $model->endTime = null;
        }

        return $model;
    }

    public static function fromLiveItem($liveItem): LiveItemFormModel
    {
        $model = new LiveItemFormModel($liveItem['filename']);

        $model->guid = $liveItem['guid'];

        $model->title = $liveItem['title'];

        $model->status = $liveItem['status'];

        $model->shortDesc = $liveItem['shortDesc'];
        $model->longDesc = $liveItem['longDesc'];

        $model->startTime = $liveItem['startTime'];
        $model->startTimeDate = $model->startTime->format(self::DATE_FORMAT);
        $model->startTimeTime = $model->startTime->format(self::TIME_FORMAT);

        $model->endTime = $liveItem['endTime'];
        $model->endTimeDate = $model->endTime->format(self::DATE_FORMAT);
        $model->endTimeTime = $model->endTime->format(self::TIME_FORMAT);

        $model->streamUrl = $liveItem['streamInfo']['url'];
        $model->streamType = $liveItem['streamInfo']['mimeType'];

        $model->authorName = $liveItem['author']['name'];
        $model->authorEmail = $liveItem['author']['email'];

        $model->customTags = $liveItem['customTags'];

        $model->coverImagePath = $liveItem['image']['path'];
        $model->coverImageUrl = $liveItem['image']['url'];
        $model->hasNewCoverImage = false;

        return $model;
    }

    public function setCoverImage(string $path)
    {
        if ($this->coverImagePath == $path) {
            return;
        }

        $this->coverImagePath = $path;
        $this->coverImageUrl = self::$config['url'] . self::$config['img_dir'] . basename($path);
        $this->hasNewCoverImage = true;
    }

    public function getCoverImageUrl(): string
    {
        if (!empty($this->coverImageUrl)) {
            return $this->coverImageUrl;
        }

        $coverImage = self::$config['liveitem_default_cover'];
        if (empty($coverImage)) {
            $coverImage = self::$config['podcast_cover'];
        }
        if (empty($coverImage)) {
            $coverImage = 'itunes_image.jpg';
        }
        return self::$config['url'] . self::$config['img_dir'] . $coverImage;
    }

    private function addValidationError(string $field, string $error)
    {
        $errors = $this->validationMessages[$field];
        if ($errors == null) {
            $this->validationMessages[$field] = array();
        }
        $this->validationMessages[$field][] = $error;
    }

    private function addMissingValueValidationError(string $field, string $fieldName)
    {
        $this->addValidationError($field, sprintf(_('%s field is missing.'), $fieldName));
    }

    public function isValid(): bool
    {
        foreach ($this->validationMessages as $field => $errors) {
            if (!empty($errors)) {
                return false;
            }
        }
        return true;
    }

    public function validate(): bool
    {
        if (empty($this->title)) {
            $this->addMissingValueValidationError('title', _('Title'));
        }

        if (
            $this->status != LIVEITEM_STATUS_ENDED
            && $this->status != LIVEITEM_STATUS_LIVE
            && $this->status != LIVEITEM_STATUS_PENDING
        ) {
            $this->addValidationError('status', _('Invalid status.'));
        }

        if ($this->startTime != null && $this->endTime != null && $this->startTime >= $this->endTime) {
            $this->addValidationError('endTime', _('End date/time must be later than start date/time.'));
        }

        if (!empty($this->authorEmail) && !filter_var($_POST['authorEmail'], FILTER_VALIDATE_EMAIL)) {
            $this->addValidationError('authorEmail', _('Invalid Author E-Mail provided'));
        }

        if (empty($this->shortDesc)) {
            $this->addMissingValueValidationError('shortDesc', _('Short Description'));
        } elseif (strlen($this->shortDesc) > 255) {
            $this->addValidationError('shortDesc', sprintf(_('Size of the \'%s\' exceeded.'), _('Short Description')));
        }

        if (!isWellFormedXml($this->customTags)) {
            $this->addValidationError('customTags', _('Custom tags are not well-formed'));
        }

        return $this->isValid();
    }

    public function apply(&$liveItem)
    {
        if (!$this->isValid()) {
            throw new Exception('Cannot apply invalid data');
        }

        $liveItem['title'] = $this->title;
        $liveItem['status'] = $this->status;
        $liveItem['startTime'] = $this->startTime;
        $liveItem['endTime'] = $this->endTime;
        $liveItem['shortDesc'] = $this->shortDesc;
        $liveItem['longDesc'] = $this->longDesc;
        $liveItem['streamInfo']['url'] = $this->streamUrl;
        $liveItem['streamInfo']['mimeType'] = $this->streamType;
        $liveItem['author']['name'] = $this->authorName;
        $liveItem['author']['email'] = $this->authorEmail;
        $liveItem['customTags'] = $this->customTags;

        if ($this->hasNewCoverImage) {
            // push existing cover image into previous covers array
            if (isset($liveItem['image']) && !empty($liveItem['image']['path'])) {
                if (!isset($liveItem['previousImages'])) {
                    $liveItem['previousImages'] = array();
                }
                array_unshift($liveItem['previousImages'], $liveItem['image']['path']);
            }

            // set live item properties
            $liveItem['image']['url'] = $this->coverImageUrl;
            $liveItem['image']['path'] = $this->coverImagePath;
        }
    }

    public function validationFor(string $field): ?string
    {
        if (!isset($this->validationMessages[$field]) || empty($this->validationMessages[$field])) {
            return null;
        }

        return implode(' ', $this->validationMessages[$field]);
    }

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
