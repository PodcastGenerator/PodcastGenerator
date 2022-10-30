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

use Lootils\Uuid\Uuid;
use PodcastGenerator\Configuration;
use DateTime;
use Exception;

class EpisodeFormModel extends FormModelBase
{
    use CoverImageTrait;

    private const DATE_FORMAT = 'Y-m-d';
    private const TIME_FORMAT = 'H:i';

    private const MAX_CATEGORIES = 3;

    private static Configuration $config;

    public static array $categoryOptions;

    public static array $yesNoOptions;

    public static array $epTypeOptions;

    public static function initialize(Configuration $config)
    {
        self::$config = $config;

        $catOpts = [];
        $cats = simplexml_load_file($config['absoluteurl'] . 'categories.xml');
        foreach ($cats as $cat) {
            $catOpts[] = ['value' => (string) $cat->id, 'label' => (string) $cat->description];
        }
        self::$categoryOptions = $catOpts;

        global $yesNoOptions;
        self::$yesNoOptions = $yesNoOptions;

        self::$epTypeOptions = [
            ['value' => 'full', 'label' => _('Full episode')],
            ['value' => 'trailer', 'label' => _('Trailer')],
            ['value' => 'bonus', 'label' => _('Bonus content')],
            ['value' => '', 'label' => _('Don\'t specify')]
        ];
    }

    private ?string $name = null;

    public function name(): ?string
    {
        return $this->name;
    }

    public ?string $guid = null;

    public string $title = '';

    public string $shortdesc = '';
    public ?string $longdesc = null;

    public array $categories = [];

    private ?int $filemtime = null;
    public string $date;
    public string $time;

    public function filemtime(): ?int
    {
        return $this->filemtime;
    }

    public function setFilemtime(int $filemtime)
    {
        $this->filemtime = $filemtime;

        if ($this->filemtime != null) {
            $this->date = date(self::DATE_FORMAT, $this->filemtime);
            $this->time = date(self::TIME_FORMAT, $this->filemtime);
        } else {
            $this->date = $this->time = '';
        }
    }

    public ?string $coverart = null;
    public ?string $coverartPath = null;
    private bool $hasNewCoverImage = false;

    public ?string $episodenum = null;
    public ?string $seasonnum = null;

    public ?string $itunesKeywords = null;

    public ?string $explicit;

    public ?string $authorname = null;
    public ?string $authoremail = null;

    public ?string $customtags = null;

    public string $episodeType = 'full';

    public ?string $itunesBlock = null;

    private function __construct(?string $name)
    {
        $this->name = $name;
        $this->explicit = self::$config['explicit_podcast'];
    }

    public static function forNewEpisode(): EpisodeFormModel
    {
        $model = new EpisodeFormModel(null);
        $model->guid = (string) Uuid::createV4();
        $model->setFilemtime(time());

        return $model;
    }

    public static function fromForm($GET, $POST): EpisodeFormModel
    {
        $model = new EpisodeFormModel(isset($GET['name']) ? $GET['name'] : null);

        $model->guid = $POST['guid'];

        $model->title = $POST['title'];

        $model->shortdesc = $POST['shortdesc'];
        $model->longdesc = $POST['longdesc'];

        $model->coverart = $POST['coverart'];

        $model->categories = $POST['category'] ?? [];

        $model->date = $POST['date'];
        $model->time = $POST['time'];

        $model->episodenum = $POST['episodenum'];
        $model->seasonnum = $POST['seasonnum'];

        $model->itunesKeywords = $POST['itunesKeywords'];
        $model->explicit = $POST['explicit'];
        $model->itunesBlock = array_key_exists('itunesBlock', $POST) ? $POST['itunesBlock'] : null;

        $model->authorname = $POST['authorname'];
        $model->authoremail = $POST['authoremail'];

        $model->customtags = $POST['customtags'];

        $model->episodeType = $POST['episodetype'];

        if (!empty($model->date) && !empty($model->time)) {
            $filemtime = strtotime($model->date . ' ' . $model->time);
            if ($filemtime !== false) {
                $model->setFilemtime($filemtime);
            } else {
                $model->addValidationError('date', _('Could not parse date/time value'));
            }
        }

        return $model;
    }

    public static function fromEpisode($episode): EpisodeFormModel
    {
        $model = new EpisodeFormModel($episode['episode']['filename']);

        $model->guid = (string) $episode['episode']['guid'];
        if (empty($model->guid)) {
            // for proper fallback, use the old method to make episode guid
            $link = str_replace(['?', '=', '$url'], '', self::$config['link']);
            $model->guid = self::$config['url'] . "?" . $link . "=" . $model->name();
        }

        $model->title = (string) $episode['episode']['titlePG'];

        $model->shortdesc = (string) $episode['episode']['shortdescPG'];
        $model->longdesc = (string) $episode['episode']['longdescPG'];

        $model->coverart = (string) $episode['episode']['imgPG'];
        $model->coverartPath = (string) $episode['episode']['imgPath'];

        $model->categories = [
            (string) $episode['episode']['categoriesPG']['category1PG'],
            (string) $episode['episode']['categoriesPG']['category2PG'],
            (string) $episode['episode']['categoriesPG']['category3PG']
        ];

        $model->setFilemtime((int) $episode['episode']['filemtime']);

        $model->episodenum = (string) $episode['episode']['episodeNumPG'];
        $model->seasonnum = (string) $episode['episode']['seasonNumPG'];

        $model->itunesKeywords = (string) $episode['episode']['keywordsPG'];
        $model->explicit = (string) $episode['episode']['explicitPG'];
        $model->itunesBlock = (string) $episode['episode']['itunesBlock'];

        $model->authorname = (string) $episode['episode']['authorPG']['namePG'];
        $model->authoremail = (string) $episode['episode']['authorPG']['emailPG'];

        $model->customtags = (string) $episode['episode']['customTagsPG'];

        $model->episodeType = (string) $episode['episode']['episodeType'];

        return $model;
    }

    public function setCoverImage(string $path)
    {
        if ($this->coverartPath == $path) {
            return;
        }

        $this->coverartPath = $path;
        $this->coverart = self::$config['url'] . self::$config['img_dir'] . basename($path);
        $this->hasNewCoverImage = true;
    }

    public function getCoverImageUrl(): string
    {
        if (!empty($this->coverart)) {
            return $this->coverart;
        }

        $coverImage = self::$config['podcast_cover'];
        if (empty($coverImage)) {
            $coverImage = 'itunes_image.jpg';
        }
        return self::$config['url'] . self::$config['img_dir'] . basename($coverImage);
    }

    public function validate(): bool
    {
        if (empty($this->title)) {
            $this->addMissingValueValidationError('title', _('Title'));
        }

        if (!empty($this->authoremail) && !filter_var($this->authoremail, FILTER_VALIDATE_EMAIL)) {
            $this->addBadValueValidationError('authoremail', _('Author E-Mail'));
        }

        if (empty($this->shortdesc)) {
            $this->addMissingValueValidationError('shortdesc', _('Short Description'));
        } elseif (strlen($this->shortdesc) > 255) {
            $this->addValidationError('shortdesc', sprintf(_('Size of the \'%s\' exceeded.'), _('Short Description')));
        }

        if (count($this->categories) > self::MAX_CATEGORIES) {
            $this->addValidationError(
                'categories',
                sprintf(_('Too many categories selected (max: %d).'), self::MAX_CATEGORIES)
            );
        }

        if (empty($this->date)) {
            $this->addMissingValueValidationError('date', _('Date'));
        }
        if (empty($this->time)) {
            $this->addMissingValueValidationError('time', _('Time'));
        }

        // Check episode and season numbers
        if (!empty($this->episodenum)) {
            if (!is_numeric($this->episodenum)) {
                $this->addBadValueValidationError('episodenum', _('Episode Number'));
            } else {
                $episodeNum = $this->episodenum + 0;
                if (!is_integer($episodeNum) || $episodeNum < 1) {
                    $this->addBadValueValidationError('episodenum', _('Episode Number'));
                }
            }
        }
        if (!empty($this->seasonnum)) {
            if (!is_numeric($this->seasonnum)) {
                $this->addBadValueValidationError('seasonnum', _('Season Number'));
            } else {
                $seasonNum = $this->seasonnum + 0;
                if (!is_integer($seasonNum) || $seasonNum < 1) {
                    $this->addBadValueValidationError('seasonnum', _('Season Number'));
                }
            }
        }

        if (empty($this->explicit)) {
            $this->addMissingValueValidationError('explicit', _('Explicit'));
        } elseif ($this->explicit != 'yes' && $this->explicit != 'no') {
            $this->addBadValueValidationError('explicit', _('Explicit'));
        }

        if (!empty($this->itunesBlock) && !in_array($this->itunesBlock, ['yes', 'no'])) {
            $this->addBadValueValidationError('itunesBlock', _('Block iTunes'));
        }

        if (self::$config['customtagsenabled'] == 'yes' && !isWellFormedXml($this->customtags)) {
            $this->addValidationError('customtags', _('Custom tags are not well-formed'));
        }

        if (!empty($this->episodetype) && !in_array($this->episodetype, ['full', 'trailer', 'bonus'])) {
            $this->addBadValueValidationError('episodetype', _('Episode Type'));
        }

        return $this->isValid();
    }

    protected function apply(&$episode)
    {
        if (empty($episode['episode']['guid'])) {
            $episode['episode']['guid'] = $this->guid;
        }

        $episode['episode']['titlePG'] = $this->title;

        $episode['episode']['episodeNumPG'] = $this->episodenum;
        $episode['episode']['seasonNumPG'] = $this->seasonnum;

        $episode['episode']['shortdescPG'] = $this->shortdesc;
        $episode['episode']['longdescPG'] = $this->longdesc;

        $catCount = count($this->categories);
        if ($catCount == 0) {
            $episode['episode']['categoriesPG']['category1PG'] = 'uncategorized';
            $episode['episode']['categoriesPG']['category2PG'] = '';
            $episode['episode']['categoriesPG']['category3PG'] = '';
        } else {
            for ($i = 0; $i < min(self::MAX_CATEGORIES, $catCount); $i++) {
                $episode['episode']['categoriesPG']['category' . ($i + 1) . 'PG'] = $this->categories[$i];
            }
        }

        $episode['episode']['keywordsPG'] = $this->itunesKeywords;
        $episode['episode']['explicitPG'] = $this->explicit;
        $episode['episode']['itunesBlock'] = $this->itunesBlock;

        $episode['episode']['authorPG']['namePG'] = $this->authorname;
        $episode['episode']['authorPG']['emailPG'] = $this->authoremail;

        if (self::$config['customtagsenabled'] == 'yes') {
            $episode['episode']['customTagsPG'] = $this->customtags;
        }

        $episode['episode']['episodeType'] = $this->episodeType;

        if ($this->hasNewCoverImage) {
            // push existing cover image into previous covers array
            if (isset($episode['episode']['imgPath']) && !empty($episode['episode']['imgPath'])) {
                if (!isset($episode['episode']['previousImgsPG'])) {
                    $episode['episode']['previousImgsPG'] = array();
                }
                array_unshift($episode['episode']['previousImgsPG'], $episode['episode']['imgPath']);
            }

            // set episode cover properties
            $episode['episode']['imgPath'] = $this->coverartPath;
            $episode['episode']['imgPG'] = $this->coverart;
        }
    }

    public function saveEpisodeMediaFile($file): string|false
    {
        $filename = basename($file['name']);

        // Error out if file is not strictly named
        if (self::$config['strictfilenamepolicy'] == 'yes') {
            if (!preg_match('/^[\w._-]+$/', $filename)) {
                $this->addValidationError(
                    'file',
                    _('Invalid filename: only A-Z, a-z, underscores, dashes and dots are permitted.')
                );
                return false;
            }
        }

        if (extension_loaded('mbstring')) {
            $filename = mb_convert_encoding($filename, 'UTF-8', mb_detect_encoding($filename));
        }

        $uploadDir = self::$config['absoluteurl'] . self::$config['upload_dir'];
        $targetFile = makeEpisodeFilename($uploadDir, $this->date, $filename);
        $targetFileWithoutExt = strtolower($uploadDir . pathinfo($targetFile, PATHINFO_FILENAME));

        $validExtensions = getSupportedFileExtensions(self::$config, ['audio', 'video']);
        $fileExt = pathinfo($targetFile, PATHINFO_EXTENSION);

        $validFileExt = in_array($fileExt, $validExtensions);
        if (!$validFileExt) {
            $this->addValidationError('file', sprintf(_('%s has invalid file extension'), $filename));
            return false;
        }

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            $this->addValidationError(
                'file',
                sprintf(_('%s was not uploaded successfully'), $filename)
            );
            return false;
        }

        $mimeType = getmime($targetFile);
        if (!$mimeType) {
            $this->addValidationError(
                'file',
                _('The uploaded episode file is not readable (permission error)')
            );
            return false;
        }

        $validMimeTypes = getSupportedMimeTypes(self::$config, ['audio', 'video']);
        $validMimeType = in_array($mimeType, $validMimeTypes);
        if (!$validMimeType) {
            $this->addValidationError(
                'file',
                sprintf(_('%s has unsupported MIME content type %s'), $filename, $mimeType)
            );
            unlink($targetFile);
            return false;
        }

        $this->name = pathinfo($targetFile, PATHINFO_BASENAME);
        return $targetFile;
    }

    public function saveCoverImageFromMediaFile(): string|null|false
    {
        if (empty($this->name)) {
            return null;
        }

        $mediaFile = self::$config['absoluteurl'] . self::$config['upload_dir'] . $this->name;
        $fileInfo = getID3Info($mediaFile);

        $coverInfo = isset($fileInfo['comments']['picture'][0]) ? $fileInfo['comments']['picture'][0] : null;
        if ($coverInfo == null) {
            return null;
        }

        $imagesDir = self::$config['absoluteurl'] . self::$config['img_dir'];

        $validMimeData = getSupportedMediaFileTypes(self::$config, ['image']);
        foreach ($validMimeData as $validMime) {
            if ($validMime['mimetype'] == $coverInfo['image_mime']) {
                $coverExt = $validMime['extension'];
                break;
            }
        }
        if (empty($coverExt)) {
            $this->addValidationError(
                'file',
                sprintf(_('%s has unsupported MIME content type %s'), _('Embedded cover art'), $coverInfo['image_mime'])
            );
            return false;
        }

        $coverFile = makeEpisodeFilename(
            $imagesDir,
            $this->date,
            pathinfo($mediaFile, PATHINFO_FILENAME) . '.' . $coverExt
        );

        if (!file_put_contents($coverFile, $coverInfo['data'])) {
            $this->addValidationError('file', _('The embedded cover art file was not saved successfully'));
            return false;
        }

        $this->setCoverImage($coverFile);
        return $coverFile;
    }
}
