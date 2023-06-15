# Switcher

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.1
### Changed
- Remove the localeAlternate() Twig function, use the default getSwitcherSites with default params instead.
- Rename the langSwitcher() Twig function to getSwitcherSites(). Note that the legacy name is still supported.

## 1.0.4
### Changed
- Changed issues and changelog URLs

## 1.0.3
### Fixed
- Fixed strict mode for loose comparison in in_array in the `buildDataForElement()` method. The strict comparaison was not working in PHP < 8.0.8

## 1.0.2
### Fixed
- Fixed a missing variable declaration for switcherValues

## 1.0.1
### Fixed
- Fixed a wrong variable name in ReadMe
- Fixed URI if entry is __home__

## 1.0.0
### Added
- Initial release
