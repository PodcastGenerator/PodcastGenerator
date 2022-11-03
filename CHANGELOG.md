# Changelog
<!-- markdownlint-configure-file {"MD024": { "allow_different_nesting": true }} -->

All notable changes to Podcast Generator will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
Versions prior to 3.2 are documented per their individual release notes.

## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security

## [3.2.9] - 2022-11-03

### Fixed

* Fixed error with unique filename generation that was causing episode creation to fail. (#721)
* Theme selection page now works properly again. (#703)
* Corrections for French translation.

## [3.2.8] - 2022-10-30

### Added

* Translation added for Arabic (Saudi Arabia).

### Changed

* PG version now shown on main admin page. (#676)

### Fixed

* File name characters that may cause problems with various file systems are now trimmed and replaced. (#691)
* Corrected variable names used in setup step 2.
* Ensured necessary variables are set before use.
* Fixed missing header on category management page.
* Fixed missing key error during episoe FTP upload processing.
* Fixed default theme to force downloading when the download button for an episode is clicked. (#714)
* Removed full file path from automatically generated episode GUIDs.
* Fixed filename problems for embedded cover art. (#715)

## [3.2.7] - 2022-06-10

### Changed

* Improved installation documentation.

### Security

* Updated Guzzle to version 6.5.7 to avoid potential HTTP downgrade issues. (CVE-2022-31042, CVE-2022-31043)

## [3.2.6] - 2022-05-26

### Fixed

* Setup step three no longer crashes on successful completion of setup. (#665)

### Security

* Updated Guzzle to version 6.5.6 to avoid possible cookie issue. (CVE-2022-29248)

## [3.2.5] - 2022-05-24

### Added

* Translations added for:
  * Farsi
  * Chinese (traditional)

### Changed

* Author name and email fields are no longer required when editing episodes (#591)

### Fixed

* Open Graph tags in the default theme did not support new cover file scheme (#592)
* Updating users no longer leads to spurious CSRF warnings

### Security

## [3.2.4] - 2022-04-20

### Changed

* German translation is now up to date
* Feed generation on setup completion has been re-enabled

### Fixed

* Certain configuration settings that were lost on upgrade are now properly persisted

## [3.2.3] - 2022-04-12

### Changed

* Rolled back initial feed generation temporarily

### Fixed

* Fixed language selection during setup

## [3.2.2] - 2022-04-11

### Fixed

* New podcast cover images are properly renamed on upload
* Setup now handles Windows directory separator when saving configuration
* Setup now generates RSS feed upon completion (#573)

## [3.2.1] - 2022-04-10

### Fixed

* Episode upload "was not uploaded successfully" (#570)

## [3.2] - 2022-04-08

### Added

* Episode search (#290)
* Webmaster and managing editor tags in RSS feed (#311)
* Admin page for listing all episodes, including future-published episodes (#349)
* Dark mode support for default theme (#416)
* Podcast namespace locked tag support (#369)
* Open Graph tags on individual episode pages for embed support (#376)
* Announce new episodes via WebSub (#379)
* Season and episode numbers for episodes (#381)
* Per-category RSS feeds (#404)
* Time zone can be set for dates and times shown on site (#446)
* Expand language selections, including national variants, for RSS feed (#450)
* Podcast namespace show GUID tag support (#473)
* Translations for:
  * Estonian
  * French
  * Hebrew
  * Swedish

### Changed

* Default theme style improvements (#282)
* Categories now listed alphabetically on categories page (#359)
* RSS feed now referenced in public web pages (#366)
* Improved installation documentation, including nginx instructions (#407, #456)
* PNG images are now supported for show cover art (#469)
* Episode cover images can now be uploaded directly when not embedded in episode media (#481)
* Short and long descriptions switched in `<description>` and `<itunes:summary>` tags (#522)

### Fixed

* Fix feed access through feed.php (#234, #367)
* Broken character escaping in HTML (#304)
* Error uploading episodes when categories are enabled (#314)
* Page navigation bar shown inappropriately on episode detail pages (#345)
* Automatically index "FTP upload" episodes on cron job (#360)
* Remove character escaping inside CDATA blocks in RSS feed (#364)
* Ensure episode GUID immutability (#370)
* Page errors when adding theme buttons (#372)
* Add character escaping in RSS feed for author (#390)
* Corrected iTunes categories (#395)
* Viewing user on admin page no longer causes incorrect CSRF warning message (#491)
* FTP indexed episodes with dash character in filename are not processed correctly (#508)
* Form label accessibility fixes
* Various typo and translation corrections
* Theme change page not showing installed themes

### Security

## Older versions

For versions 3.0 through 3.1.x see the respective release notes documents.
Release notes and changelog details are not available for Podcast Generator 2.7
or older.
