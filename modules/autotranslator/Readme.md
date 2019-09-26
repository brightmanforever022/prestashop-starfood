Module is installed in a regular way – simply upload your archive and click install

CHANGELOG:
===========================
v 2.7.1 (August 14, 2017)
===========================
-[*] Extended compatibility with some specific server settings

Files modified
-----
- /autotranslator.php

===========================
v 2.7.0 (July 7, 2017)
===========================
- [+] Translate articles/categories of module "Amazzing blog"
- [+] Translate meta fields for controller pages, available in "Preferences > SEO & URLs" menu
- [+] Display number of processed characters on configuration page
- [*] Automatically re-build search index for new translations
- [*] Minor fixes and optimizations

Files modified
-----
- /autotranslator.php
- /views/css/back.css
- /views/js/back.js
- /views/templates/admin/configure.tpl
- /views/templates/admin/key-form.tpl
- /views/templates/admin/list.tpl
- /readme_en.pdf

Files added
-----
- /upgrade/install-2.7.0.php

===========================
v 2.6.4 (May 1, 2017)
===========================
- [+] Translate articles/categories of module "ph_simpleblog"
- [*] Fixed recognition of translatable strings in overriden module templates

Files modified
-----
- /autotranslator.php
- /views/js/back.js

===========================
v 2.6.3 (March 30, 2017)
===========================
- [*] Fixed error "Unsupported media type"

Files modified
-----
- /autotranslator.php

===========================
v 2.6.2 (March 23, 2017)
===========================
- [*] Added support for English with GB iso code
- [*] Minor fix for retro-compatibility with PS 1.6.0.6

Files modified
-----
- /autotranslator.php

===========================
v 2.6.1 (March 11, 2017)
===========================
- [*] Don't interrupt bulk translation if error appears, and show this error to user

Files modified
-----
- /autotranslator.php
- /views/css/back.css
- /views/js/back.js


===========================
v 2.6.0 (February 10, 2017)
===========================
- [+] Added documentation
- [*] Extended compatibility for PS versions 1.6.0.4 - 1.6.0.9 and and 1.7+
- [*] New user-friendly listings with ajax sorting and pagination
- [*] Added IDs to category filter, for easier selection
- [*] Added filtering by manufacturer

Files modified
-----
- /autotranslator.php
- /views/css/back.css
- /views/js/back.js
- /views/templates/admin/key-form.tpl
- /translations/es.php
- /translations/fr.php
- /translations/ru.php

Files added
-----
- /readme_en.pdf
- /views/templates/admin/configure.tpl
- /views/templates/admin/list.tpl
- /views/templates/admin/pagination.tpl
- /translations/uk.php
- /translations/ro.php

Files removed
-----
- /views/templates/admin/content-types-form.tpl

===========================
v 2.5.5 (October 3, 2016)
===========================
- [+] Autosave overwriting option
- [*] Truncate object fields before saving, if required
- [*] Minor bug fixes

Files modified
-----
- /autotranslator.php
- /views/js/back.js

===========================
v 2.5.4 (March 28, 2016)
===========================
- [*] Keep selected category when pagination or ordering are changed
- [*] Extended compatibility with different server configurations (ssl_verify_peer false)

Files modified
-----
- /autotranslator.php
- /views/js/back.js

===========================
v 2.5.3 (January 24, 2016)
===========================
- [+] Filter products by categories
- [*] Misc bug fixes

Files modified
-----
- /autotranslator.php
- /views/js/back.js
- /views/templates/admin/content-types-form.tpl

===========================
v 2.5.2 (September 8, 2015)
===========================
- [+] Translations for manufacturers
- [+] Translations for suppliers
- [+] Translations for product tags
- [+] Translations for product image legends
- [+] Option to keep or overwrite existing translations
- [-] Removed requirement for having English in the list of installed langauges
- [*] Optimized queries for generating lists. Thousands of products can be displayed without timeout errors
- [*] PSR-2

Files modified
-----
- /autotranslator.php
- /views/js/back.js
- /views/css/back.css
- /logo.png

Files added
-----
- /views/templates/index.php
- /views/templates/admin/index.php
- /views/templates/admin/key-form.tpl
- /views/templates/admin/content-types-form.tpl

===========================
v 2.5.1 (July 25, 2015)
===========================
- [+] Possibility to add module translations to theme directory
- [*] Notification if a new file was created

Files modified
-----
- /autotranslator.php
- /views/js/back.js

===========================
v 2.5.0 (May 12, 2015)
===========================
Added
-----
- Possibility to translate attributes, attribute groups, features, feature values
- Stop button for bulk translations

Fixed
-----
- Pagination, filtering, sorting

Files modified
-----
- /autotranslator.php
- /views/css/back.css
- /views/js/back.js
- logo.gif
- logo.png

===========================
v 2.0.0 (March 16, 2015)
===========================
Added
-----
- Possibility to translate modules, themes, categories, cms pages, cms categories
- Extended resource translations: including meta tags + link_rewrite generation basing on resource name
- Possibility to translate resources to all languages in one click

Changed
-----
- Optimized bulk translation
- Slightly modified settings interface
- Other code fixes and optimizations

Directories moved
-----
- /css/ -> /views/css/
- /js/ -> /views/js/

Files modified
-----
- /autotranslator.php
- /css/back.css
- /js/back.js

Files added
-----
- /Readme.md

Directories removed
-----
- /controllers/

===========================
v 1.0.0 (September 01, 2014)
===========================
Initial relesase
