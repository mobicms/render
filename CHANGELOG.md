# Changelog

All notable changes to this project will be documented in this file.  
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).  
Detailed changes can see in the [repository log].


## [Unreleased]

#### Added
- Nothing

#### Changed
- Bumped minimum PHP version to 8.0

#### Deprecated
- Nothing

#### Removed
- Nothing

#### Fixed
- Nothing

#### Security
- Nothing


## [3.0.0] - 2021-07-29

#### Added
- `Engine::addPath()`  
  Instead of three arguments of the old addFolder() method (namespace, default folder and search array),
  two arguments with a folder and optional namespace are used.

#### Changed
- Specifying a namespace now is not mandatory.  
  If it is not specified, it will be used `main::` as default.
- `Engine::getFolder()` renamed to `Engine::getPath()`
- Various code improvements

#### Removed
- `Engine::addFolder()`
- `TemplateFunftion` class


## [2.1.0] - 2021-07-28

#### Added
- PHP 8.x support
- Source code static analysis using Psalm
- Checking coding standards using PHP_CodeSniffer
- Test coverage report and code quality rating using Scrutinizer-CI
- CI services using GitHub Actions
  
#### Changed
- Used latest version of PhpUnit
- Various code improvements

#### Removed
- Drop support for PHP older than 7.4


## [2.0.0] - 2019-12-31

#### Removed
- Template::end()
- Template::insert()


## [1.1.0] - 2019-12-11

#### Added
- Template::sectionAppend()
- Template::sectionReplace()
  
#### Deprecated
- Template::end()
- Template::insert()


## [1.0.1] - 2019-12-08

#### Changed
- Small internal improvements


## [1.0.0] - 2019-12-05
The development of this package started on the basis of [league/plates v.3.3.0](https://github.com/thephpleague/plates/releases/tag/3.3.0).  
The purpose of the development was to simplify the source code as much as possible, get rid of the unnecessary and add the missing functionality.

Here are the most significant changes compared to the original packag.

#### Added
- Each namespace can have one default (fallback) folder and optional several search folders.
  The template is searched sequentially across all of these folders, from the last to the first.
  The first template found will be used.
  If not found (or not specified), it will use the template specified by default.

#### Changed
- All code rewritten to use PHP 7.2 or newer
- All tests rewritten to use new PhpUnit 8.x
- Specify a namespace and its path is mandatory.  
  Now you cannot call template without specifying a namespace.
- Namespace refactoring

#### Removed
- Folder with example.
- Documentation (After editing will be added again).
- Removed all extensions that were delivered with the package.
- Due to replacement with a new algorithm, removed old fallback folder functionality.
- Removed existing classes and methods:  
  `[D]` completely removed as unnecessary  
  `[C]` covered by new functionality  
  `[S]` replaced with simpler code
  - `[S] Directory::class`
  - `[S] FileExtension::class`
  - `[S] Folder::class`
  - `[S] Folders::class`
  - `[S] Functions::class`
  - `[C] Engine::setDirectory()`
  - `[C] Engine::getDirectory()`
  - `[D] Engine::removeFolder()`
  - `[D] Engine::dropFunction()`
  - `[D] Engine::loadExtensions()`
  - `[D] Engine::path()`
  - `[D] Engine::exists()`
  - `[S] Engine::make()`
- Some other code that is not used.

[Unreleased]: https://github.com/mobicms/render/compare/3.0.0...HEAD
[3.0.0]: https://github.com/mobicms/render/compare/2.1.0...3.0.0
[2.1.0]: https://github.com/mobicms/render/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/mobicms/render/compare/1.1.0...2.0.0
[1.1.0]: https://github.com/mobicms/render/compare/1.0.1...1.1.0
[1.0.1]: https://github.com/mobicms/render/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/mobicms/render/compare/segregation...1.0.0
[repository log]: https://github.com/mobicms/render/commits/
