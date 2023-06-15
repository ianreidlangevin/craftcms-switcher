# Switcher

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.1
### Changed
- Breaking: The localeAlternate() Twig function has been removed. Instead, use the default getSwitcherSites function with default parameters.
- The Twig function previously known as langSwitcher() has been renamed to getSwitcherSites(). However, the legacy name remains supported for backward compatibility.
- The readme has been updated with more detailed usage instructions.

## 1.0.4
### Updated
- The URLs for issues and the changelog have been modified.

## 1.0.3
### Fixed
- Resolved strict mode issues in buildDataForElement() method for loose comparison in in_array. The strict comparison wasn't functional in PHP versions earlier than 8.0.8.

## 1.0.2
### Fixed
- Rectified a missing declaration for the switcherValues variable.

## 1.0.1
### Fixed
- Rectified a mistaken variable name in the ReadMe.
- Fixed URI if entry is set to home

## 1.0.0
### Introduced
- Initial release of the plugin
