# videojs-generate-karma-config

[![Build Status](https://travis-ci.org/videojs/videojs-generate-karma-config.svg?branch=master)](https://travis-ci.org/videojs/videojs-generate-karma-config)
[![Greenkeeper badge](https://badges.greenkeeper.io/videojs/videojs-generate-karma-config.svg)](https://greenkeeper.io/)
[![Slack Status](http://slack.videojs.com/badge.svg)](http://slack.videojs.com)

[![NPM](https://nodei.co/npm/videojs-generate-karma-config.png?downloads=true&downloadRank=true)](https://nodei.co/npm/videojs-generate-karma-config/)

Currently our karma configs are the same for most plugins, but when the default config changes a bit, every repository has
to be updated since it is a static file. This package will provide the standard config as a module, so that updates can be
deployed much easier.

Lead Maintainer: Brandon Casey [@brandonocasey](https://github.com/brandonocasey)

Maintenance Status: Stable


<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [Installation](#installation)
- [Options](#options)
  - [`files`](#files)
  - [`browsers`](#browsers)
  - [`preferHeadless`](#preferheadless)
  - [`serverBrowsers`](#serverbrowsers)
  - [`customLaunchers`](#customlaunchers)
  - [`ciLaunchers`](#cilaunchers)
  - [`browserstackLaunchers`](#browserstacklaunchers)
  - [`coverage`](#coverage)
  - [`showQUnitUI`](#showqunitui)
  - ['reporters'](#reporters)
- [Code Coverage](#code-coverage)
  - [codecov.io](#codecovio)
  - [View the html report](#view-the-html-report)
  - [View the report after testing](#view-the-report-after-testing)
- [Overriding Configuration Options](#overriding-configuration-options)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Installation

```
$ npm install --save-dev karma videojs-generate-karma-config
```

Then in your karma config do

```js
const generateKarmaConfig = require('videojs-generate-karma-config');

module.exports = function(config) {
  const options = {};

  config = generateKarmaConfig(config, options);
};
```
## Options
By default all options are passed as the second argument to generateKarmaConfig.

### `files`

> Type: `Function`
> Default: `none`
> NOTE: Be very careful with this option, this will effect ci runs as well.

A function that should take one argument, the array of files that are included, and return an array of files that should be included. This is used to manually overide the files that are included ina test run

Default files that will be passed to you function
```js
[
  'node_modules/video.js/dist/video-js.css',
  'dist/*.css',
  'node_modules/sinon/pkg/sinon.js',
  'node_modules/video.js/dist/video.js',
  'test/dist/bundle.js'
]
```

Example with files function:

```js
module.exports = function(config) {
  const options = {
    files(defaultFiles) {
      return defaultFiles.concat([
        'some-other-file.js'
      ]);
    }
  };

  config = generateKarmaConfig(config, options);
};
```

### `browsers`

> Type: `Function`
> Default: `none`

> NOTE: Be very careful with this option, this will effect ci runs as well.

A function that should take one argument, the array of browsers that are about to run, and return an array of browsers that should run. This is used to manually overide the browsers that should run.

Example with detected browsers:

```js
module.exports = function(config) {
  const options = {
    browsers(aboutToRun) {
      // never test on Safari
      return aboutToRun.filter(function(launcherName) {
        return launcherName !== 'Safari';
      });
    }
  };

  config = generateKarmaConfig(config, options);
};
```

### `preferHeadless`

> Type: `Boolean`
> Default: `true`

If we should prefer running headless browsers. This will change the defaults for `ciLaunchers` as well as automatic browser detection. Make sure to handle this in [`browsers`](###browsers)

### `serverBrowsers`

> Type: `Function`
> Default: `none`

A function that should return an array of browsers that should run when in static server mode (--single-run=false). It should take one argument: The default serverBrowsers array which is `[]`.

Example:

```js
module.exports = function(config) {
  const options = {
    serverBrowsers(defaults) {
      serverBrowsers.push('myTestLauncher');

      return serverBrowsers;
    }
  };

  config = generateKarmaConfig(config, options);
};
```

### `customLaunchers`

> Type: `Function`
> Default: `none`

A function that should return an object of karma custom launchers. It should take one argument: The default custom launchers object which is: `{}`;

Example:

```js
module.exports = function(config) {
  const options = {
    customLaunchers(defaults) {
      return Object.assign(defaults, {
        myTestLauncher: {
          base: 'ChromeHeadless'
        }
      };
    }
  };

  config = generateKarmaConfig(config, options);
};
```

### `ciLaunchers`

> Type: `Function`
> Default: `{}`

> NOTE: All browsers contained from this object will be run on [ci](https://www.npmjs.com/package/is-ci) unless BROWSER_STACK_USERNAME is in the enviornment!

A function that should return an object containing karma custom launchers, that should all be run on ci. It should take one argument.: The default ci launchers object which empty is:

Example:

```js
module.exports = function(config) {
  const options = {
    ciLaunchers(defaults) {
      // add another browser to travis testing
      return Object.assign(defaults, {
        myTestLauncher: {
          base: 'ChromeHeadless'
        }
      };
    }
  };

  config = generateKarmaConfig(config, options);
};
```

### `browserstackLaunchers`

> Type: `Function`
> Default: `none`

> NOTE: all browsers contained in this list will be run if there is an enviornment variable called BROWSER_STACK_USERNAME present!

A function that should return an object containing karma custom launchers, that should all be run on browserstack. It should take one argument: The default browserstack launchers object which is:
```js
{
  bsChrome: {
    base: 'BrowserStack',
    browser: 'chrome',
    os: 'Windows',
    os_version: '10'
  },

  bsFirefox: {
    base: 'BrowserStack',
    browser: 'firefox',
    os: 'Windows',
    os_version: '10'
  },

  bsSafariSierra: {
    base: 'BrowserStack',
    browser: 'safari',
    os: 'OS X',
    os_version: 'Sierra'
  },

  bsEdgeWin10: {
    base: 'BrowserStack',
    browser: 'edge',
    os: 'Windows',
    os_version: '10'
  },

  bsIE11Win10: {
    base: 'BrowserStack',
    browser: 'ie',
    browser_version: '11',
    os: 'Windows',
    os_version: '10'
  },

  bsIE11Win7: {
    base: 'BrowserStack',
    browser: 'ie',
    browser_version: '11',
    os: 'Windows',
    os_version: '7'
  }
}
```

```js
module.exports = function(config) {
  const options = {
    BrowserstackLaunchers(defaults) {
      // only test on Edge windows 10
      return {
        bsEdgeWin10: defaults.bsEdgeWin10;
      };
    }
  };

  config = generateKarmaConfig(config, options);
};
```
For more information on [browserstack launchers see the docs](https://github.com/karma-runner/karma-browserstack-launcher).


### `coverage`

> Type: `Function`
> Default: `true`

If we should report test coverage or not, by default we do.

Example with coverage turned off

```js
module.exports = function(config) {
  const options = {
    coverage: false
  };

  config = generateKarmaConfig(config, options);
};
```

### `showQUnitUI`

> Type: `Boolean`
> Default: `false` if in single-run mode, `true` otherwise

Show the QUnit UI in non-debug runs of Karma. This sets both `client.clearContext = false` and `client.qunit.showUI: true`.

Having `clearContext` turned off increases test reliability. However, sometimes, showing the QUnit UI during test runs is useful to track down a test that is timing out or failing, particularly on CI/Browserstack.

### 'reporters'

> Type: 'Array'
> Default: 'dots' for CI and 'progress' for local.

If passed in, the value passed will be used. If coverage is turned on, and reporters is being set, 'coverage' must be included in the list. 'coverage' is removed from the list if the `coverage` setting is `false`.

## Code Coverage
lcov, json, and html coverage reports will be generated in `test/dist/coverage` after a test run. Unless coverage is set to false.

### codecov.io
1. install codecov globally in your ci of choice
2. run `codecov -f test/dist/coverage/lcov.info` on your ci after testing

### View the html report
> NOTE: When running as a static server the "serverBrowsers" will have to finish running before you see this. See [serverBrowsers](###serverBrowsers)
1. Run your unit tests
2. open `test/dist/coverage/index.html`

### View the report after testing
* simply run `cat test/dist/coverage/text.txt` or if you want a cross platform way use `shx`. `shx cat test/dist/coverage/text.txt`

## Overriding Configuration Options

Any Karma settings that have not been exposed as an option can be overriden after calling `generateKarmaConfig`.

Example:

```js
module.exports = function(config) {
  const options = {};

  config = generateKarmaConfig(config, options);

  // The reporters setting is not exposed as an option currently
  config.reporters = ['spec'];
};
```
