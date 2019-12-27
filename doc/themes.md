# Themes
Podcast Generator Version 3 got a complete new theme system. All older themes are incompatbile with the new one.
PHP knowledge might be useful but not required for it.
As PG 3 uses a MVC arch, you should keep in mind that the theme files are actually PHP files which perform simple PHP actions (mostly loops).

## How a theme is made
Every theme needs at least 4 files!
In the theme directory, create the following files:
* categories.php
* index.php
* preview.png
* theme.json

The first two files are actually files where the user interacts directly with. The first one is the start page and the second one lists all categories.
The preview.png file is just a screenshot of the theme and theme.json includes some meta data.
The `theme.json` looks like this:
```
{
    "name": "THEME NAME",
    "description": "A simple description for the theme",
    "version": "1.0",
    "pg_versions": ["3.0", "3.1"],
    "author": "YOUR NAME",
    "credits": "Can be empty"
}
```
The `version` field is the current **theme** version. The **pg_versions** array says with which PG Versions this theme is compatible.