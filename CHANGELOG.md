# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.4] - 2017-07-13
### Fixed
- Fix problem with compatible EE CE block and cms page (EE compatibility)
    - Remove `$eavColumnName` from `fetchData–url_rewrite` always uses `entity_id`
    - Use `$eavColumnName` for `cms_page_store` table page reference
- Fixed issue with not saving data when user not blur the input before save

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
### Changes
- Rendering rewrited and moved to more front-end friendly palces, to make customizations easier
