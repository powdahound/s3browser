Amazon S3 Directory Browser
===========================

Please report any issues [here on GitHub][issues].

Installation
------------
**NOTE:** S3 Directory Browser is currently designed to run on its own subdomain. e.g.: [http://files.powdahound.com][ph]

1. Check out the latest release from GitHub:

        cd /srv/www
        git clone git@github.com:powdahound/s3browser.git

2. Tweak config to your liking. Each option is documented in the config file.

        cp config-sample.php config.php
        vim config.php

3. Add an Apache VirtualHost for your new subdomain. e.g.:

        <VirtualHost *:80>
          ServerName s3browser.example.com
          DocumentRoot /srv/www/s3browser

          <Directory />
            AllowOverride all
            Order allow, deny
            Allow from all
          </Directory>
        </VirtualHost>

4. Reload your Apache config:

        sudo /etc/init.d/apache2 reload

[ph]: http://files.powdahound.com
[issues]: http://github.com/powdahound/s3browser/issues

