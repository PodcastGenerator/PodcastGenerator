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

    public static function initialize(Configuration $config)
    {
        self::$config = $config;

        $catOpts = [];
        $cats = simplexml_load_file($config['absoluteurl'] . 'categories.xml');
        foreach ($cats as $cat) {
            $catOpts[] = ['value' => (string) $cat->id, 'label' => (string) $cat->description];
        }
        self::$categoryOptions = $catOpts;
    }

    private ?string $name = null;

    public function name(): ?string
    {
        return $this->name;
    }

    public ?string $guid;

    public string $title = '';

    public string $shortdesc = '';
    public ?string $longdesc;

    public array $categories;

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

        if ($this->filemtime != null)
        {
            $this->date = date(self::DATE_FORMAT, $this->filemtime);
            $this->time = date(self::TIME_FORMAT, $this->filemtime);
        } else {
            $this->date = $this->time = '';
        }
    }

    public ?string $coverart;
    public ?string $coverartPath;
    private bool $hasNewCoverImage = false;

    public ?string $episodenum;
    public ?string $seasonnum;

    public ?string $itunesKeywords;

    public ?string $explicit;

    public ?string $authorname;
    public ?string $authoremail;

    public ?string $customtags;

    private function __construct(?string $name)
    {
        $this->name = $name;
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

        $model->authorname = $POST['authorname'];
        $model->authoremail = $POST['authoremail'];

        $model->customtags = $POST['customtags'];

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

        $model->authorname = (string) $episode['episode']['authorPG']['namePG'];
        $model->authoremail = (string) $episode['episode']['authorPG']['emailPG'];

        $model->customtags = (string) $episode['episode']['customTagsPG'];

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

        if (self::$config['customtagsenabled'] == 'yes' && !isWellFormedXml($this->customtags)) {
            $this->addValidationError('customtags', _('Custom tags are not well-formed'));
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

        $episode['episode']['authorPG']['namePG'] = $this->authorname;
        $episode['episode']['authorPG']['emailPG'] = $this->authoremail;

        if (self::$config['customtagsenabled'] == 'yes') {
            $episode['episode']['customTagsPG'] = $this->customtags;
        }

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
}
