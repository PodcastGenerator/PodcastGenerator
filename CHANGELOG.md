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

## [3.2] - yyyy-MM-dd

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
