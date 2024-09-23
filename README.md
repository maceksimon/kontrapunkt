# How to run via Docker

1. Install [ddev](https://ddev.readthedocs.io/en/stable/#installation)
2. Start docker with command `ddev start` in project root directory
3. Import database with command `ddev import-db --src=db.sql.gz` (replace with actual db name)
4. Run `ddev describe` to view docker settings
5. Use `ddev stop`

# How to login?

Visit [https://kontrapunkt.ddev.site/user](https://kontrapunkt.ddev.site/wp-login.php)

- Default username: maceksimon
- Default email address: maceksimonlmd@gmail.com

You can reset password with command `ddev wp user update maceksimon --user_pass=xxxxx`

# How to modify theme?

- Theme is located in `/wp-content/themes/kontrapunkt`
- Do not forget to set `define( 'WP_DEBUG', true );` to see changes in twig templates
- Do not forget to set `define( 'GOOGLE_MAPS_API_KEY', 'xxx' );`
- Static template is located in `/wp-content/themes/kontrapunkt/static`
- We use **yarn** package manager for development `/wp-content/themes/kontrapunkt/static/package.json`
- For quick preview without WordPress installed you can visit `https://kontrapunkt.ddev.site/styleguide`

# How to export DB

`wp db export - --single-transaction --skip-lock-tables --quick --ssh=HOST_ADDRESS.117.79 --path=public_html | gzip > /Users/simon/Projects/personal/kontrapunkt/db.sql.gz`
`wp db export - --single-transaction --skip-lock-tables --quick | gzip > /Users/simon/Projects/personal/kontrapunkt/db.sql.gz`

# How to import DB

`gzip -c -d db.sql.gz | wp db import -`

# How to replace fixed URLs (ACF escape values we need 2 commands)

`wp search-replace 'https://kontrapunkt.ddev.site' 'https://wpstarter.cz' --skip-columns=guid`
`wp search-replace 'https:\/\/kontrapunkt.ddev.site' 'https:\/\/wpstarter.cz' --skip-columns=guid`

# How to update?

`wp core update --locale=cs_CZ`
`wp plugin update --all`
`wp theme update --all`
`wp language core update`
`wp language plugin install cs_CZ --all`
`composer update --working-dir='wp-content/themes/kontrapunkt'`

# How to deploy

`rsync -rltzvP --delete --filter="merge,p- /Users/simon/Projects/personal/kontrapunkt/rsync-exclude.txt" /Users/simon/Projects/personal/kontrapunkt/ HOST_ADDRESS.117.79:public_html/`
`wp timber clear_cache_twig --ssh=HOST_ADDRESS.117.79 --path=public_html`
`wp breeze purge --cache=all --ssh=HOST_ADDRESS.117.79 --path=public_html`

# How to backup

`rsync -rltzvP --delete --filter="merge,p- /Users/simon/Projects/personal/kontrapunkt/rsync-exclude.txt" HOST_ADDRESS.117.79:public_html/ /Users/simon/Projects/personal/kontrapunkt/`

# How to translate

Use Poedit to extract translatable strings from twig templates. Strings will be autoloaded from file.

`_x('Read more', 'kontrapunkt', 'kontrapunkt')`
