See this online document:
http://podcastgen.sourceforge.net/documentation/FAQ-localization

--
Extra info:
Translations of Podcast Generator are maintained by volunteers here:
https://www.transifex.com/projects/p/podcast-generator/

The file languages.xml in this folder contains a list of languages
with their ID (IETF format) and description (name in original language).
New language can be added to the XML file, they will appear automatically
in admin -> Change your podcast details -> Feed Language

To enable a localization of Podcast Generator add a folder called according to the
IEFT language code (e.g. en_EN), which contains a sub-folder LC_MESSAGES
that include two files "messages.po" and "messages.mo" (localizations).
See existing localization as an example. 
As soon as the new folder and the aforementioned files will be created, 
the language will automatically be available in
admin -> Change Podcast Generator Configuration -> Podcast Generator Language