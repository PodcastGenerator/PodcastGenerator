#!/bin/sh
# Execute this file from the root of the git repository
# This file generates the translation template and the english base
# ./contrib/generateTranslation.sh <VERSIONTAG>
xgettext --copyright-holder="The Podcast Generator Translation Team" --no-wrap --package-name="Podcast Generator" --package-version="$1" --language=PHP --from-code=UTF-8 -o PodcastGenerator/components/locale/messages.pot PodcastGenerator/admin/*.php PodcastGenerator/core/*.php PodcastGenerator/*.php