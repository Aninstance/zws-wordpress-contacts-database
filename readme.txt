=== ZWS Contacts Database === 

Donate link: https://www.zaziork.com/donate
Contributors: zaziork
Tags: contacts, database, contacts database, google map, distance calculator, contacts location database, postcode calculator
Requires at least: 3.0 
Tested up to: 4.3.0
Stable tag: 0.5
License: GPLv2 or later
Plugin to create and administer a contacts database and calculate nearest contacts to any given UK postcode.

== Description ==

This is a plugin to create and administer a contacts database and calculate the nearest contacts to any given UK postcode.

An example use case: The plugin is being developed for use initially on a wildlife hospital website, to allow people to register as "wildlife ambulances".

When a casualty is reported, the administrator enters the postcode, and is presented with the 5 closest contacts ("ambulances") to the casualty who are within their specified maximum travel distance. The casualty and the contacts are also plotted on a Google map.

== Beta Information ==

This is a beta testing release. The first release version (1.0) of this plugin is currently still under active development.

If you are installing from the master zip file from GitHub, please re-download the zip and reinstall periodically, to ensure you always have the latest release version.

== Features ==

* Contacts submit details into database. Details include:
    - First and last name
    - Postcode
    - Contact phone number
    - Email address
    - Maximum radius prepared to travel to target
    - Free form extra information
* Administrators submit postcode, which queries database and returns nearest contacts to the target (provided target is within contact's maximum radius).
* Contact information for the nearest contacts to the target are displayed, together with a Google Map upon which the target, contacts and home base are marked.
* Uses Memcached to cache requests to the Google Distance Matrix API, to improve speed and limit API requests.
* Administrators can browse the full database of submitted contacts.
* Configurable map icons (administrators can set URL in admin page).
* Contacts are requested to select the days and times that they would be available when registering.
* Only contacts who have indicated that they are available on the current day of the week are displayed when searching for the 'nearest contact'.

== Requirements ==

You will need to have Memcached installed on your system to make use of the Cache feature.

The plugin has been tested with PHP versions 5.5.x and above.

== Future development roadmap ==

* Add facility for administrators EDIT and DELETE from the database.
* Add feature to allow contacts to modify their own stored data. This would necessitate creating a password at sign-up.
* Add function to email administrators when new contacts register.
* All selection of country, to extend functionality to other countries besides the UK; e.g. USA zipcodes, then lock down to country specific searches.
* Swap out the target postcode form, for AJAX address search form, and use that when calculating distances instead of: target postcode => contacts postcodes.
* Add ability for administrator to select not to include the day/time feature. When not included, remove section from registration form, times button from display pages, and submit defaults of 00:00 to database).
* Add presentation polish.
* Refactor code to tidy up the mess!

== Plugin Website ==

The URL of this plugin's website is: https://www.zaziork.com/zws-contacts-database/

The URL of this plugin's Wordpress page is: [Not yet published in the WordPress directory].

== Installation ==

To install search for "ZWS Contacts Database" in the WordPress Plugins Directory, then click the "Install Now" button. [Not yet published in the WordPress directory].

When it's installed, simply activate, then navigate to the Settings page to update the defaults to your liking.

Alternatively, the latest version of the plugin may be installed via a zip file, available here: https://github.com/ZWS2014/zws-wordpress-contacts-database/archive/master.zip

After downloading the zip, change the name of the unzipped directory to "ZwsContactsDatabase", upload the plugin to the '/wp-content/plugins/' directory, then activate through the 'Plugins' menu in WordPress.

Once installed, the contacts submission form can be added to a post or page using the shortcode: 

[zwscontactsdatabase_public_form].

The administration page where contacts may be viewed and displayed on a map in relation to a "target" postcode can be inserted to a post or page using the shortcode:

[zwscontactsdatabase_results_page].

Be sure to visit the plugin's settings page (available via the Wordpress admin section settings sidebar), to configure the plugin for your system.

You will also need to create a page (or post) for your website's privacy policy, and enter the URL on the aforementioned settings page.

== Frequently Asked Questions ==

There are no frequently asked questions as yet.

== Current version ==

The current version is: 0.5

== Changelog ==

= 0.5 =

* Added ability to set an option for the initial Google Map zoom factor from the settings menu.

= 0.4 =

* Added ability for administrators to define "home base" postcode and name from the settings page.
* Made memcached key unique in order to allow multiple instances of the plugin to use the same memcached server.
* [SECURITY FIX] Fixed a bug where some input may not have been correctly filtered.

= 0.3 =

* Ability for contacts to select the days and times that they're available added.
* Ability for administrators to view the days/times that contacts are available when accessing the full database via the administration dashboard.
* When using the nearest contacts search, only contacts available "today" are shown.

= 0.2 =

* Settings page added, with ability to change configuration options.

= 0.1 =

First beta version of the plugin.

== Support ==

The plugin is to be used entirely at the user's own risk.

Support and/or implementation of feature requests are not guaranteed, however comments and/or requests for free support are welcome. 

For premium support, please contact the author via the plugin website: https://www.zaziork.com/contact
