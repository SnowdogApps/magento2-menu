# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
- Fix menu item border style and duplicated item key on menu drop

## [2.8.0] - 2018-11-06
- Fix true/false values for saved target checkbox
- Fix drag and drop behaviour for edited menu
- Preliminary support for Magento 2.3

## [2.7.0] - 2018-08-13
### Added
- Add api endpoints

## [2.6.0] - 2018-07-2
### Added
- Product title method
- Price formatter method

### Fixed
- Product id binding

## [2.5.0] - 2018-05-23
### Added
- Add product node type
- Add the ability to change node type
- Enhance admin panel UI using VueJS

### Fixed
- Fix saving target blank for custom url

### Removed
- Remove frontend javascript and icon

## [2.4.0] - 2018-01-29
### Changed
- Module doesn't support 2.0 anymore

### Fixed
- Fixed compatibility with Magento Commerce (Enterprise)
- Imporved compatibility with Magento 2.2

## [2.3.1] - 2018-01-18
### Fixed
- Fixed issue with overwrite category node

## [2.3.0] - 2018-01-17
### Added
- Added target option for custom link to open in a new tab
- Added save and continue button

### Fixed
- Fixed issue with node templates if it's more than one menu

## [2.2.3] - 2018-01-09
### Removed
- Remove package version from composer.json

## [2.2.2] - 2018-01-09
### Added
- getMenuCssClass to menu block (menu is null check)

## [2.2.1] - 2018-01-04
### Fixed
- Delete node button action
## [2.2.0] - 2018-01-03
### Added
- Ability to overwritte templates per menu ID

### Changed
- Formatting of `menu.phtml` template
- #1 - Better autocomplete
- Styling of node action buttons

### Fixed
- Sql queries when table prefix is set in Magento (#28)

## [2.1.0] - 2017-10-03
### Changed
- Update composer.json for compatibility with M2.2 (#18)

### Fixed
- Fix method declaration compatibility in node type blocks (#9)
- Fix example layout xml code in README.md (#12)
- Fix showing error on browser console with eventListeners who calling element which doesn't exist (#13)

### Removed
- Remove PHP7 type hints (#11)

## [2.0.5] - 2017-07-18
### Fixed
- Fix problem with compatible CE cms page

## [2.0.4] - 2017-07-17
### Fixed
- Fix problem with compatible EE CE block and cms page (EE compatibility)
- Remove `$eavColumnName` from `fetchData–url_rewrite` always uses `entity_id`
- Use `$eavColumnName` for `cms_page_store` table page reference
- Fix issue with not saving data when user not blur the input before save

## [2.0.3] - 2017-07-13
### Fixed
- Implements DB Wrapper for EE and CE version (EE compatibility)

## [2.0.2] - 2017-07-10
### Fixed
- Missing css_class column in table (incorrect versioning in upgrade script)

## [2.0.1] - 2017-07-10
### Fixed
- Fix for undefined offset in CmsPage block

## [2.0.0] - 2017-06-03
### Changed
- Rendering rewrited and moved to more front-end friendly palces, to make customizations easier
