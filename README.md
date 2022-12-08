# `mobicms/render`

[![GitHub](https://img.shields.io/github/license/mobicms/render)](https://github.com/mobicms/render/blob/main/LICENSE)
[![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/mobicms/render)](https://github.com/mobicms/render/releases)
[![Packagist](https://img.shields.io/packagist/dt/mobicms/render)](https://packagist.org/packages/mobicms/render)

[![CI-Analysis](https://github.com/mobicms/render/workflows/analysis/badge.svg)](https://github.com/mobicms/render/actions?query=workflow%3AAnalysis)
[![CI-Tests](https://github.com/mobicms/render/workflows/tests/badge.svg)](https://github.com/mobicms/render/actions?query=workflow%3ATests)
[![Sonar Coverage](https://img.shields.io/sonar/coverage/mobicms_render?server=https%3A%2F%2Fsonarcloud.io)](https://sonarcloud.io/code?id=mobicms_render)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=mobicms_render&metric=alert_status)](https://sonarcloud.io/summary/overall?id=mobicms_render)

**mobicms/render is a native PHP template system** that started on the basis of [league/plates v.3.3.0](https://github.com/thephpleague/plates/releases/tag/3.3.0).
The development of the original package went in a direction that was not suitable for our projects where Plates was used, so it was decided to continue development as an independent package.
Our goal was to simplify the source code as much as possible, get rid of the unnecessary and add the missing functionality.

This package is part of [mobiCMS](https://github.com/mobicms/mobicms) and [JohnCMS](https://github.com/johncms/johncms),
but can be used freely in any other projects.
  
## Installation

The preferred method of installation is via [Composer](http://getcomposer.org). Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require mobicms/render
```


## Documentation

Check out the [documentation wiki](https://github.com/mobicms/render/wiki) for detailed information
and code examples.


## Contributing
Contributions are welcome! Please read [Contributing][contributing] for details.

[![YAGNI](https://img.shields.io/badge/principle-YAGNI-blueviolet.svg)][yagni]
[![KISS](https://img.shields.io/badge/principle-KISS-blueviolet.svg)][kiss]

In our development, we follow the principles of [YAGNI][yagni] and [KISS][kiss].
The source code should not have extra unnecessary functionality and should be as simple and efficient as possible.


## License

This package is licensed for use under the MIT License (MIT).  
Please see [LICENSE][license] for more information.


## Our links
- [**mobiCMS Project**][website] website and support forum
- [**GitHub**](https://github.com/mobicms) mobiCMS project repositories
- [**Twitter**](https://twitter.com/mobicms)

[website]: https://mobicms.org
[yagni]: https://en.wikipedia.org/wiki/YAGNI
[kiss]: https://en.wikipedia.org/wiki/KISS_principle
[contributing]: https://github.com/mobicms/render/blob/main/.github/CONTRIBUTING.md
[license]: https://github.com/mobicms/render/blob/main/LICENSE
