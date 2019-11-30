# Changelog
This project follows [semantic versioning](https://semver.org/).  
All notable changes to this project will be documented in this file.  
Detailed change can see in the [repository log](https://github.com/mobicms/render/commits/).

## 1.0.0 - Unreleased
The development of this package started on the basis of [league/plates v.3.3.0](https://github.com/thephpleague/plates/releases/tag/3.3.0).  
The purpose of the development was to simplify the source code as much as possible, get rid of the unnecessary and add the missing functionality.

**Here are the most significant changes compared to the [original package](https://github.com/thephpleague/plates/releases/tag/3.3.0).**

#### Added
- Nothing

#### Changed
- All code rewritten to use PHP 7.2 or newer
- All tests rewritten to use new PhpUnit 8.x
- Namespace refactoring

#### Deprecated
- Nothing

#### Removed
- Folder with example.
- Documentation (After editing will be added again).
- Removed all extensions that were delivered with the package.
- Due to replacement with a new algorithm, removed old fallback folder functionality.
- Removed existing classes and methods that are not used `[D]`, replaced with simpler code `[S]`
  or covered by new functionality `[C]`:
  - `[D] Directory::class`
  - `[S] FileExtension::class`
  - `[S] Folders::class`
  - `[C] Engine::setDirectory()`
  - `[C] Engine::getDirectory()`
  - `[D] Engine::removeFolder()`
  - `[D] Engine::dropFunction()`
  - `[D] Engine::loadExtensions()`
  - `[D] Engine::path()`
  - `[D] Engine::exists()`
  - `[S] Engine::make()`
- Some other code that is not used.

#### Fixed
- Nothing

#### Security
- Nothing
