# Changelog

## 0.8.5
* Fixed bug with oAuth2 token not being renewed.
* Added `Region taxonomy name` field to the settings. This is for partners that use a custom taxonomy to add
  a region to their post

## 0.8.4
* We now support featured images when saving a post to ABC Manager

## 0.8.3
* Don't send articles to ABC when they are already send from ABC

## 0.8.2
* Implement extra credentials check
* Fix some logics on retrieving bearer token from ABC
* Rename some labels for fields
* Add notice on classic editor when the post is successfully send to ABC Manager

## 0.8.1
* Added a `composer.json` with scripts to check code quality, using tools like PHPStan and PHP CodeSniffer.
  * Run `composer phpcheck` to check the code.
  * Run `composer phpfix` to attempt to automatically resolve any issue.
* Performed code cleanup.

# Upgrading
* Run `composer install` to install the above mentioned tools.

## 0.8.0
* Updated API authentication with OAuth tokens.
* Allow iframe HTML tags.

## 0.7.0
* Added an API connection status indicator to the setting page.

## 0.6.3
* Removed dead code.

## 0.6.2
* Added Dutch documentation.
* Updated the README.md.

## 0.6.1
* Removed GitHub Access Token authentication, the repository is now publicly accessible.
* Updated the README.md with installation steps.
