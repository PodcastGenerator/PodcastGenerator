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

class LiveItemFormModel extends FormModelBase
{
    use CoverImageTrait;

    private const DATE_FORMAT = 'Y-m-d';
    private const TIME_FORMAT = 'H:i';

    private static Configuration $config;

    private ?string $name;

    public function name(): ?string
    {
        return $this->name;
    }

    public ?Uuid $guid;

    public string $title = '';

    public string $status = LIVEITEM_STATUS_PENDING;

    public string $shortDesc = '';
    public ?string $longDesc = null;

    private ?DateTime $startTime = null;
    public string $startTimeDate;
    public string $startTimeTime;

    public function startTime(): ?DateTime
    {
        return $this->startTime;
    }

    private function setStartTime(?DateTime $time)
    {
        $this->startTime = $time;

        if ($this->startTime != null) {
            $this->startTimeDate = $time->format(self::DATE_FORMAT);
            $this->startTimeTime = $time->format(self::TIME_FORMAT);
        } else {
            $this->startTimeDate = $this->startTimeTime = '';
        }
    }

    private ?DateTime $endTime = null;
    public string $endTimeDate;
    public string $endTimeTime;

    public function endTime(): ?DateTime
    {
        return $this->endTime;
    }

    private function setEndTime(?DateTime $time)
    {
        $this->endTime = $time;

        if ($this->endTime != null) {
            $this->endTimeDate = $time->format(self::DATE_FORMAT);
            $this->endTimeTime = $time->format(self::TIME_FORMAT);
        } else {
            $this->endTimeDate = $this->endTimeTime = '';
        }
    }

    public ?string $streamUrl = null;
    public ?string $streamType = null;

    public ?string $authorName = null;
    public ?string $authorEmail = null;

    public ?string $customTags = null;

    public ?string $coverImagePath = null;
    public ?string $coverImageUrl = null;
    private bool $hasNewCoverImage = false;

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

    private function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public static function forNewLiveItem(): LiveItemFormModel
    {
        $model = new LiveItemFormModel(null);
        $model->guid = Uuid::createV4();
        $model->setStartTime(null);
        $model->setEndTime(null);

        return $model;
    }

    public static function fromForm($GET, $POST): LiveItemFormModel
    {
        $model = new LiveItemFormModel(isset($GET['name']) ? $GET['name'] : null);

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
            $startTime = new DateTime($model->startTimeDate . ' ' . $model->startTimeTime);
            $model->setStartTime($startTime);
        } catch (Exception $e) {
            $model->addValidationError('startTime', $e->getMessage());
            $model->startTime = null;
        }

        try {
            $endTime = new DateTime($model->endTimeDate . ' ' . $model->endTimeTime);
            $model->setEndTime($endTime);
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

        $model->setStartTime($liveItem['startTime']);
        $model->setEndTime($liveItem['endTime']);

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

    protected function apply(&$liveItem)
    {
        $liveItem['title'] = $this->title;
        $liveItem['status'] = $this->status;
        $liveItem['startTime'] = $this->startTime;
        $liveItem['endTime'] = $this->endTime;
        $liveItem['shortDesc'] = $this->shortDesc;
        $liveItem['longDesc'] = $this->longDesc;
        $liveItem['streamInfo'] = [ 'url' => $this->streamUrl, 'mimeType' => $this->streamType ];
        $liveItem['author'] = [ 'name' => $this->authorName, 'email' => $this->authorEmail ];
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
            $liveItem['image'] = [ 'url' => $this->coverImageUrl, 'path' => $this->coverImagePath ];
        }
    }
}
