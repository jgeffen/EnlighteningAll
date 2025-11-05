<a name="8.1.0"></a>
# [8.1.0](https://github.com/videojs/videojs-generate-karma-config/compare/v8.0.1...v8.1.0) (2024-05-20)

### Features

* update browser/os versions ([#43](https://github.com/videojs/videojs-generate-karma-config/issues/43)) ([1ad2049](https://github.com/videojs/videojs-generate-karma-config/commit/1ad2049))

### Chores

* cleanup package.json and bump node ([#46](https://github.com/videojs/videojs-generate-karma-config/issues/46)) ([be3ef1d](https://github.com/videojs/videojs-generate-karma-config/commit/be3ef1d))
* **package:** update dependencies ([#42](https://github.com/videojs/videojs-generate-karma-config/issues/42)) ([fa15518](https://github.com/videojs/videojs-generate-karma-config/commit/fa15518))
* **package:** update dependencies to fix vulnerabilities ([#45](https://github.com/videojs/videojs-generate-karma-config/issues/45)) ([f248808](https://github.com/videojs/videojs-generate-karma-config/commit/f248808))
* Update dependencies ([#47](https://github.com/videojs/videojs-generate-karma-config/issues/47)) ([17281c4](https://github.com/videojs/videojs-generate-karma-config/commit/17281c4))

<a name="8.0.1"></a>
## [8.0.1](https://github.com/videojs/videojs-generate-karma-config/compare/v8.0.0...v8.0.1) (2021-06-24)

### Chores

* update dependencies ([00e867a](https://github.com/videojs/videojs-generate-karma-config/commit/00e867a))

<a name="8.0.0"></a>
# [8.0.0](https://github.com/videojs/videojs-generate-karma-config/compare/v7.1.0...v8.0.0) (2021-05-13)

### Features

* drop ie 11, old edge, safari 9, add safari 14 ([#40](https://github.com/videojs/videojs-generate-karma-config/issues/40)) ([ccc0f64](https://github.com/videojs/videojs-generate-karma-config/commit/ccc0f64))

<a name="7.1.0"></a>
# [7.1.0](https://github.com/videojs/videojs-generate-karma-config/compare/v7.0.1...v7.1.0) (2021-01-20)

### Features

* reporters option, default to progress ([#39](https://github.com/videojs/videojs-generate-karma-config/issues/39)) ([1b7044d](https://github.com/videojs/videojs-generate-karma-config/commit/1b7044d))
* turn off qunit ui unless in server mode, add option to turn it on  ([#38](https://github.com/videojs/videojs-generate-karma-config/issues/38)) ([396fcda](https://github.com/videojs/videojs-generate-karma-config/commit/396fcda))

<a name="7.0.1"></a>
## [7.0.1](https://github.com/videojs/videojs-generate-karma-config/compare/v7.0.0...v7.0.1) (2020-12-07)

### Chores

* update qunit ([10f6d84](https://github.com/videojs/videojs-generate-karma-config/commit/10f6d84))

<a name="7.0.0"></a>
# [7.0.0](https://github.com/videojs/videojs-generate-karma-config/compare/v6.0.0...v7.0.0) (2020-11-09)

### Features

* support any ci ([#37](https://github.com/videojs/videojs-generate-karma-config/issues/37)) ([1af972d](https://github.com/videojs/videojs-generate-karma-config/commit/1af972d))

### Chores

* update package versions ([a0e7e01](https://github.com/videojs/videojs-generate-karma-config/commit/a0e7e01))


### BREAKING CHANGES

* drop support `travisLaunchers` and `teamcityLaunchers` in favor of a more generic `ciLaunchers` and detection of ci using the using `is-ci` package.
* drop support for the `detectBrowsers` option as it was only used in one specific scenario that would cause an error anyway. Browsers are always detected when no browsers are set via the karma config or karma runner cli.

<a name="6.0.0"></a>
# [6.0.0](https://github.com/videojs/videojs-generate-karma-config/compare/v5.3.1...v6.0.0) (2020-10-08)

### Features

* **BREAKING:** default to no headless server browsers ([#36](https://github.com/videojs/videojs-generate-karma-config/issues/36)) ([019b9cb](https://github.com/videojs/videojs-generate-karma-config/commit/019b9cb))


### BREAKING CHANGES

* **BREAKING:** for most projects server browsers are turned off, we
should stop defaulting to using them.

<a name="5.3.1"></a>
## [5.3.1](https://github.com/videojs/videojs-generate-karma-config/compare/v5.2.1...v5.3.1) (2019-08-19)

> Note: this release and v5.2.1 are the same

### Bug Fixes

* pin karma-browserstack-launcher@~1.4.0, prevents browserstack timeouts ([#28](https://github.com/videojs/videojs-generate-karma-config/issues/28)) ([cd17a4c](https://github.com/videojs/videojs-generate-karma-config/commit/cd17a4c))

<a name="5.3.0"></a>
## [5.3.0](https://github.com/videojs/videojs-generate-karma-config/compare/v5.2.1...v5.3.0) (2019-04-24)

> Note: this version was released in error

### Chores

* **package:** update karma-qunit to ^3.0.0 ([#27](https://github.com/videojs/videojs-generate-karma-config/issues/27)) ([8465cf8](https://github.com/videojs/videojs-generate-karma-config/commit/8465cf8))

<a name="5.2.1"></a>
## [5.2.1](https://github.com/videojs/videojs-generate-karma-config/compare/v5.2.0...v5.2.1) (2019-06-26)

### Bug Fixes

* pin karma-browserstack-launcher@~1.4.0, prevents browserstack timeouts ([#28](https://github.com/videojs/videojs-generate-karma-config/issues/28)) ([cd17a4c](https://github.com/videojs/videojs-generate-karma-config/commit/cd17a4c))

### Chores

* **package:** update karma-qunit to ^3.0.0 ([#27](https://github.com/videojs/videojs-generate-karma-config/issues/27)) ([8465cf8](https://github.com/videojs/videojs-generate-karma-config/commit/8465cf8))

<a name="5.2.0"></a>
# [5.2.0](https://github.com/videojs/videojs-generate-karma-config/compare/v5.1.0...v5.2.0) (2019-04-12)

### Features

* Use karma-safari-applescript-launcher to prevent `open` dialog in safari ([#25](https://github.com/videojs/videojs-generate-karma-config/issues/25)) ([d37a4d7](https://github.com/videojs/videojs-generate-karma-config/commit/d37a4d7))

<a name="5.1.0"></a>
# [5.1.0](https://github.com/videojs/videojs-generate-karma-config/compare/v5.0.2...v5.1.0) (2019-03-06)

### Features

* no coverage report in server mode ([#23](https://github.com/videojs/videojs-generate-karma-config/issues/23)) ([388a6e2](https://github.com/videojs/videojs-generate-karma-config/commit/388a6e2))

### Bug Fixes

* **security:** fix security issues by updating package-lock.json ([9bb5156](https://github.com/videojs/videojs-generate-karma-config/commit/9bb5156))

### Chores

* switch to [@videojs](https://github.com/videojs)/generator-helpers ([351325e](https://github.com/videojs/videojs-generate-karma-config/commit/351325e))

### Documentation

* postDetection was renamed to browsers ([29d8ffd](https://github.com/videojs/videojs-generate-karma-config/commit/29d8ffd))

<a name="5.0.2"></a>
## [5.0.2](https://github.com/videojs/videojs-generate-karma-config/compare/v5.0.1...v5.0.2) (2019-01-10)

### Chores

* **package:** more dependency updates ([2eaef2d](https://github.com/videojs/videojs-generate-karma-config/commit/2eaef2d))

<a name="5.0.1"></a>
## [5.0.1](https://github.com/videojs/videojs-generate-karma-config/compare/v5.0.0...v5.0.1) (2019-01-10)

### Chores

* **package:** update all dependencies ([#22](https://github.com/videojs/videojs-generate-karma-config/issues/22)) ([ceb0016](https://github.com/videojs/videojs-generate-karma-config/commit/ceb0016))

### Documentation

* update README to show how to override config settings ([#21](https://github.com/videojs/videojs-generate-karma-config/issues/21)) ([1fd9c48](https://github.com/videojs/videojs-generate-karma-config/commit/1fd9c48))

<a name="5.0.0"></a>
# [5.0.0](https://github.com/videojs/videojs-generate-karma-config/compare/v4.0.2...v5.0.0) (2018-11-15)

### Features

* remove IE11 Win7 from testing ([#16](https://github.com/videojs/videojs-generate-karma-config/issues/16)) ([b2bc096](https://github.com/videojs/videojs-generate-karma-config/commit/b2bc096))

### Bug Fixes

* Pin qunit to ~2.5.1 to fix stack trace issues ([#18](https://github.com/videojs/videojs-generate-karma-config/issues/18)) ([2c55b93](https://github.com/videojs/videojs-generate-karma-config/commit/2c55b93))
* Update deps to fix a few npm audit warnings ([#15](https://github.com/videojs/videojs-generate-karma-config/issues/15)) ([83a8a4f](https://github.com/videojs/videojs-generate-karma-config/commit/83a8a4f))


### BREAKING CHANGES

* Remove IE11 Win7

<a name="4.0.2"></a>
## [4.0.2](https://github.com/videojs/videojs-generate-karma-config/compare/v3.1.0...v4.0.2) (2018-10-29)

### Features

* Add an option to turn off coverage ([#11](https://github.com/videojs/videojs-generate-karma-config/issues/11)) ([12c22a6](https://github.com/videojs/videojs-generate-karma-config/commit/12c22a6))
* add browser disconnect timeout ([#9](https://github.com/videojs/videojs-generate-karma-config/issues/9)) ([c1a80b6](https://github.com/videojs/videojs-generate-karma-config/commit/c1a80b6))
* browser stack name and build should be the same ([c718ddb](https://github.com/videojs/videojs-generate-karma-config/commit/c718ddb))
* Turn of video and local settings for tests ([#12](https://github.com/videojs/videojs-generate-karma-config/issues/12)) ([b8d1704](https://github.com/videojs/videojs-generate-karma-config/commit/b8d1704))
* update safari to Mojave, and add safari El Capitan ([#10](https://github.com/videojs/videojs-generate-karma-config/issues/10)) ([91d444c](https://github.com/videojs/videojs-generate-karma-config/commit/91d444c))

### Bug Fixes

* add a space between build number and pr for browserstack ([#8](https://github.com/videojs/videojs-generate-karma-config/issues/8)) ([c5c1c26](https://github.com/videojs/videojs-generate-karma-config/commit/c5c1c26))

### Documentation

* add files to README ([314a346](https://github.com/videojs/videojs-generate-karma-config/commit/314a346))


### BREAKING CHANGES

* Test on a newer/older version of Safari by default

<a name="3.1.0"></a>
# [3.1.0](https://github.com/videojs/videojs-generate-karma-config/compare/v3.0.0...v3.1.0) (2018-10-24)

### Bug Fixes

* add safari tech preview launcher ([#7](https://github.com/videojs/videojs-generate-karma-config/issues/7)) ([08c014a](https://github.com/videojs/videojs-generate-karma-config/commit/08c014a)), closes [#6](https://github.com/videojs/videojs-generate-karma-config/issues/6)

<a name="3.0.0"></a>
# [3.0.0](https://github.com/videojs/videojs-generate-karma-config/compare/v2.0.0...v3.0.0) (2018-08-31)

### Features

* Prefer headless browsers and add options ([#3](https://github.com/videojs/videojs-generate-karma-config/issues/3)) ([5d10adc](https://github.com/videojs/videojs-generate-karma-config/commit/5d10adc))

### Chores

* upgrade husky ([3783653](https://github.com/videojs/videojs-generate-karma-config/commit/3783653))


### BREAKING CHANGES

* We now prefer headless browsers by default

<a name="2.0.0"></a>
# [2.0.0](https://github.com/videojs/videojs-generate-karma-config/compare/v1.0.1...v2.0.0) (2018-08-28)

### Features

* code coverage reporting ([#2](https://github.com/videojs/videojs-generate-karma-config/issues/2)) ([6520296](https://github.com/videojs/videojs-generate-karma-config/commit/6520296))


### BREAKING CHANGES

* adds a new package.

<a name="1.0.1"></a>
## [1.0.1](https://github.com/videojs/videojs-generate-karma-config/compare/v1.0.0...v1.0.1) (2018-08-22)

### Bug Fixes

* include dist css ([b2bf05c](https://github.com/videojs/videojs-generate-karma-config/commit/b2bf05c))

### Chores

* update tooling ([de7c8b4](https://github.com/videojs/videojs-generate-karma-config/commit/de7c8b4))

<a name="1.0.0"></a>
# 1.0.0 (2018-08-22)

### Features

* initial ([#1](https://github.com/videojs/videojs-generate-karma-config/issues/1)) ([d4675af](https://github.com/videojs/videojs-generate-karma-config/commit/d4675af))

