ZF::Col - PHP movie collection manager
======================================

This app is used for managing your personal collection of movies. 
It is intended to run on-line (on a webserver) so that you can share
your collection with your friends and family. 
The app is written in PHP & Zend Framework. Please see the INSTALL.txt
file for installation instructions.


CHANGELOG
=========

Version 1.0 | Realease date: November 1 2012

- Initial release
- Features in this release
    * Simple (one-click) movie adding using one of two pre-installed movie parsers
        * themoviedb.org parser 1.0
        * CSFD parser 1.0 (for Czech speaking movie fans)
        * Of course, manual adding is supported
    * User management & ACL
        * Simply add new users who can manage your collection
        * Select a role for him/her, either:
        * Administrator: allowed to manage the whole app and movies; or
        * Editor: allowed to add / edit / remove movies & media types
    * Easily manage your media types
    * Favorite movies support
    * Create PDF output of your collection
    * Movie trailers are enabled (not included in CSFD parser)
    * Home page personalization
    * Simple movie statistics
    * Localization support (app is shipped with English & Czech translations)
    * Multi-language movie information selection
    * Various databases supported (this version is shipped with MySQL adapter only)
    * Various app fonts enabled


AUTHOR
======
Kamil Kantar (kamil.kantar@me.com)
http://macpoint.github.com/zfcol


LICENSE
=======
This application is licensed under the new BSD which is bundled with its package
in the file LICENSE.txt. You can also read the license at http://opensource.org/licenses/bsd-3-clause
