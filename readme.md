[![Build Status](https://travis-ci.org/tagmeo/installer.svg?branch=master)](https://travis-ci.org/tagmeo/installer) [![Latest Stable Version](https://poser.pugx.org/tagmeo/installer/v/stable)](https://packagist.org/packages/tagmeo/installer) [![Latest Unstable Version](https://poser.pugx.org/tagmeo/installer/v/unstable)](https://packagist.org/packages/tagmeo/installer) [![License](https://poser.pugx.org/tagmeo/installer/license)](https://packagist.org/packages/tagmeo/installer) [![composer.lock](https://poser.pugx.org/tagmeo/installer/composerlock)](https://packagist.org/packages/tagmeo/installer)

Tagmeo uses [Composer](http://getcomposer.org) to manage its dependencies. So, before using [Tagmeo](https://github.com/tagmeo/tagmeo), make sure you have Composer installed on your machine.

## Installation

First, download the Tagmeo installer using Composer:

```bash
composer global require tagmeo/installer
```

Make sure to place the `~/.composer/vendor/bin` directory (or the equivalent for your OS) in your `PATH` so the `tagmeo` executable can be located by your system.

Once installed, the `tagmeo new` command will create a fresh Tagmeo installation in the directory you specify. For example, `tagmeo new blog` will create a directory named `blog` containing a fresh installation with all of the dependencies already installed. This method of installation is much faster than installing via Composer:

```bash
tagmeo new blog
cd blog
php tagmeo setup
```

The last command will walk you through the setup process to create your environment file, generate WordPress authentication keys and salts, install NPM packages, run Gulp, and provision your virtual machine.