#!/bin/sh
# Execute this file from the root of the git repository
# This file generates the translation template and the english base
# ./contrib/devtools/generateTranslation.sh <VERSIONTAG>
xgettext \
    --copyright-holder="The Podcast Generator Translation Team" \
    --package-name="Podcast Generator" \
    --package-version="$1" \
    --msgid-bugs-address="https://github.com/PodcastGenerator/PodcastGenerator/issues/new" \
    --language=PHP \
    --add-comments="#" \
    --from-code=UTF-8 \
    -o PodcastGenerator/components/locale/messages.pot \
    PodcastGenerator/*.php \
    PodcastGenerator/admin/*.php \
    PodcastGenerator/core/*.php \
    PodcastGenerator/core/misc/*.php \
    PodcastGenerator/setup/*.php \
    PodcastGenerator/themes/default/*.php \
