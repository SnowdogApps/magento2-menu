# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [Unreleased]

## [2.20.0] - 2022-09-26
### Added
- `url_key` on some node types ([#227](https://github.com/SnowdogApps/magento2-menu/pull/227))
### Fixed
- Selecting item in admin between two stores ([#91](https://github.com/SnowdogApps/magento2-menu/issues/91))
- Node issue during menu duplication ([#247](https://github.com/SnowdogApps/magento2-menu/issues/247))

## [2.19.0] - 2022-05-04
### Added
- Allow to set submenu template via layout xml ([#223](https://github.com/SnowdogApps/magento2-menu/issues/223))
- Fix menu stores save issue when single store mode enabled ([#226](https://github.com/SnowdogApps/magento2-menu/issues/226))
- Fix duplicate keys detected for nodes ([#228](https://github.com/SnowdogApps/magento2-menu/issues/228))
- Fix drag and drop nodes duplication when editing menu nodes ([#241](https://github.com/SnowdogApps/magento2-menu/issues/241))
- Ajax loader to node image upload and remove Ajax JS code (#79674)
- An error handler to node image remove Ajax JS code (#79674)
- Set errors HTTP response code in node admin controller image upload and delete actions (#79674)
- Fix loading nodes based on large catalog ([#232](https://github.com/SnowdogApps/magento2-menu/issues/232))

### Changed
- Improve JSON response in node admin controller image delete action (#79674)
- Improve the menu node image upload error message (#79674)

### Fixed
- Image for new nodes set to blank (#79674)
- import from categories ([#236](https://github.com/SnowdogApps/magento2-menu/issues/236))

## [2.18.0] - 2021-11-23
### Added
- Menu node type GraphQL interface feature ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- `nodes` field to GraphQL menu type ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- `node_template` and `submenu_template` fields to GraphQL menu node type ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- Store ID filtering support to menu repository `getList` method ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- All menu items list support to GraphQL `snowdogMenus` query ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- Default store filtering support to GraphQL `snowdogMenus` and `snowdogMenuNodes` queries ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))

### Changed
- Make the menu duplicate feature execute in a DB transaction (#80889)
- Refactor menu duplicate feature code in menu admin controller save and duplicate actions (#80889)
- Don't print messages about custom nodes templates if they are not set (#82414)
- GraphQL menu custom URL node `target` field data type to boolean ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- Set non-nullable fields in GraphQL schema ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- Handle duplicate and empty values in GraphQL `snowdogMenus` query `identifiers` argument ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))
- Memoize loaded menus in GraphQL menu data provider to improve nodes loading performance ([#209](https://github.com/SnowdogApps/magento2-menu/issues/209))

### Fixed
- Menu node image file cloner error handling (#80889)
- Product node type get removed after changing node position and entering the invalid product ID (#81485)
- Handle menu child nodes of pending parent nodes in nodes export tree processor ([#206](https://github.com/SnowdogApps/magento2-menu/issues/206))
- Sort menu nodes according to their positions in nodes export tree processor ([#206](https://github.com/SnowdogApps/magento2-menu/issues/206))

## [2.17.0] - 2021-09-03
### Changed
- Improve menu save process node product type validator error messages (#81256)

### Fixed
- Menu node position reset issues on menu save (#81257)

## [2.16.0] - 2021-07-21
### Added
- Creation time, update time and store view columns to menu admin grid (#69080)
- Save button options list to menu admin edit page (#69085)
- Menu duplicate feature (#69085)
- Import categories and subcategories feature (#70196)

### Changed
- `snowmenu_menu_list.xml` delete mass action to Magento `2.1` XML format in order to match the rest of the XML in the file (#69080)
- Syntax highlight GraphQL code blocks in readme file (#69080)
- Move menu controller save action complex logic to a seperate class and refactor accordingly (#69085)
- Move admin block import page buttons class files under `Adminhtml` directory (#80035)
- Move menu admin block edit page nodes tab class file under `Adminhtml` directory (#80035)
- Move menu admin block edit page buttons class files under `Edit` directory (#80035)

### Fixed
- Remove the duplicate listing toolbar `massaction` tag in `snowmenu_menu_list.xml` (#69080)
- custom templates list in select (#79677)
- An infinite loop in large node trees with catalog product nodes save process (#80360)

### Removed
- Unused CMS WYSIWYG config model class in menu admin edit page nodes tab block (#80035)

## [2.15.0] - 2021-06-08
### Added
- enable/disable menu option (#69084)

### Updated
- update lodash to `4.17.21` in npm dependencies
- update vue nodes - pass data to admin ui component (#69084)

### Fixed
- Enabled state checkbox (#181)
- option.store.join is not a function error (#73516)
- Make importing menu CSS class field optional (#79104)

## [2.14.0] - 2021-03-30
### Added
- Node validation classes (#73442)
- DB table columns listing method to menu and node resource models (#70197)
- Menu YAML import/export feature (#70197)
- Nodes custom templates documentation (#74567)
- Menu node image field (#70218)
- Treeselect for category selector (#69126)
- GraphQL feature (#76409)
- Treeselect for child category selector (#76417)

### Changed
- Improve node product validation error message on menu save (#73442)
- Make menu CSS class field optional in menu admin edit page (#70197)

### Fixed
- Prevent creating nodes with invalid product IDs (#73442)
- configuration for child category type node (#74222)
- Custom templates for node types `wrapper` and `custom_url` (#74936)
- Change some API interfaces getters return types to `mixed[]` in order to fix a Swagger error (#75295)

## [2.13.0] - 2020-11-16
### Added
- Node model additional data setter and getter methods (#94, #69088)
- Functionality to enable/disable specific menu node (#85, #65561)
- Functionality to remove specific menu node from menus list view, using Actions or Select dropdown (#69083)
- Possibility to set a template for "node" and "submenu" template per node (#84, #65549)
- Invalidate page cache on menu changes (#115, #70191)
- Image helper to resize product image (#95, #70199)

### Changed
- Index page title in admin panel (#69078)
- Change menu model stores save method return type to boolean (#115, #70191)
- Bump GitHub backend workflow `MCS Check` checkout repository actions to v2 (#115, #70191)
- Change imported menu model to API data interface in menu admin controller save action (#71279)
- Move menu admin controller save action `execute` method current nodes list code to a separate method (#71279)
- Replace existing nodes `in_array` check with `isset` in menu admin controller save action `execute` method (#71279)
- Documentation to clarify how to add a new node type (#69, #69072)
- Render some of vue components list on server side to improve process of adding a new node type (#69, #69072)

### Fixed
- Prevent menu stores save if there are no store changes (#115, #70191)
- Prevent unnecessary menu data save due to data values types changes (#115, #70191)
- Prevent unnecessary menu nodes data save due to enabled object data changes flag in menu node repository get list method items (#115, #70191)
- A menu save issue that prevents deleting all nodes of a menu (#71279)
- Correct a misspelled variable in menu admin controller save action `execute` method (#71279)
- Issue with flush cache after model is saved (#135, #71585)
- Phpdoc for getJsonConfig method (#134, #71576)
- Menu save issue that prevents deleting all nodes of a menu (#71279)

### Removed
- An unnecessary `if` statement in menu admin controller save action `_convertTree` method (#71279)

## [2.12.0] - 2020-08-24
### Added
- PHP 7.4 support (#118)
- Magento 2.4 support (#119)

### Changed
- Move submenu template path to protected variable (#109)

## [2.11.1] - 2020-05-15
### Fixed
- `array_filter()` error for `cmsBlock`, `cmsPage` nodes, if opening `edit-menu-page` from admin panel (#106)

## [2.11.0] - 2020-04-14
### Added
- Static code analysis tools
- Showing store scope in CMS block and page selects

### Changed
- Updated UI
- Updated frontend dependencies
- PHP code formatted to match PHPCS Magento coding standard rules
- Vue components props update to match ESlint Vue Recommended rules

## [2.10.2] - 2020-04-12
### Fixed
- Overflow for main container - #100

## [2.10.1] - 2020-03-23
### Fixed
- Nodes are deleted and recreated every time a menu is saved (#97)
- Typo in a layout file (#96)

## [2.10.1] - 2020-03-23
### Fixed
- Nodes are deleted and recreated every time a menu is saved (#97)
- Typo in a layout file (#96)

## [2.10.0] - 2019-05-29
### Added
- Provide access to entity associated with menu node (#83)
- New API endpoint to get nodes by identifier, additional information to the response (#70)

## [2.9.0] - 2019-04-21
### Added
- Wrapper node

## [2.8.4] - 2019-02-18
### Fixed
- Missing url when a cms page has no url rewrites - #76

## [2.8.3] - 2019-02-17
### Fixed
- Disable overflow for opened panel - #78 #79

## [2.8.2] - 2019-02-12
### Fixed
- Product node images fixed - #73 #74

## [2.8.1] - 2018-12-19
### Fixed
- Fix menu item border style and duplicated item key on menu drop

## [2.8.0] - 2018-11-06
### Fixed
- Fix true/false values for saved target checkbox
- Fix drag and drop behaviour for edited menu

### Added
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
