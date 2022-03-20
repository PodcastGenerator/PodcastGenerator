# Themes

Podcast Generator Version 3 got a complete new theme system. All older themes
are incompatible with the new one.
PHP knowledge might be useful but not required for it.
As PG 3 uses a MVC architecture, you should keep in mind that the theme files
are actually PHP files which perform simple PHP actions (mostly loops).

## How a theme is made

Every theme needs at least 4 files!
In the theme directory, create the following files:

* `categories.php`
* `index.php`
* `preview.png`
* `theme.json`

The first two files are actually files that the user directly interacts with.
The first one is the start page and the second one lists all categories.
The `preview.png` file is just a screenshot of the theme and `theme.json`
includes some metadata regarding the theme.

The `theme.json` looks like this:

```json
{
    "name": "THEME NAME",
    "description": "A simple description for the theme",
    "version": "1.0",
    "pg_versions": ["3.0", "3.1"],
    "author": "YOUR NAME",
    "credits": "Can be empty"
}
```

The `version` field is the current **theme** version. The **pg_versions** array
says with which PG Versions this theme is compatible.

## The PHP part

Themes indeed do some PHP stuff but not too complicated stuff. This is done
mostly to display episodes and categories.
However you don't really need PHP knowledge when creating a theme.
Just edit the stuff that is provided by echo.
If you need any help with creating themes, feel free to open an issue here on
GitHub :)

## How to create a theme

Unfortunately, this documentation isn't really good. The best way to start is
learning by doing. Copy the `themes/default` folder to `themes/yourname`.
And make some modifications to it so you get a feeling on how the theme system
works.