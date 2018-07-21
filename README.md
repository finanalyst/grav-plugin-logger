# Var Dumper Plugin

Provides a mechanism for logging data inside another GRAV plugin. Want to see the value of a variable at some line in another plugin. Uses the same routines as `dump`, but records the output to a time-stamped html file, rather than in the debug window, or at the top of the screen. Thus the data is not overwritten by a redirect. For development.

The **Var-Dumper** Plugin is for [Grav CMS](http://github.com/getgrav/grav).

## Installation

Installing the Logger plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install var-dumper

This will install the Logger plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/var-dumper`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `var-dumper`. You can find these files on [GitHub](https://github.com/finanalyst/grav-plugin-var-dumper) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/lvar-dumper

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.
Also best with DataManager to visualise the logged data. So DataManager is included as a dependency.

### Admin Plugin

If you use the admin plugin, you can install directly through the admin plugin by browsing the `Plugins` tab and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/var-dumper/var-dumper.yaml` to `user/config/plugins/var-dumper.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: false
```
>WARNING: This plugin should not be used for production, so the default is *false*.

Note that if you use the admin plugin, a file with your configuration, and named var-dumper.yaml will be saved in the `user/config/plugins/` folder once the configuration is saved in the admin.

## Usage

Add the following where you want to record a message or variable in log file:
```php
    $this->grav['dd']->dump('some message');
    $this->grav['dd']->dump('another message', 'another-file');
    $this->grav['dd']->dump($some-variable);
```
`dump` parameters:
1. any php string or object
1. the basename of the file to which the message is written. A timestamp is appended to the file basename. The file extension is `.html`.

## Viewing dump files

Using the DataManger plugin, the files can be view by clicking on the type 'var-dumps'. All the dumps are then listed in the window.

Clicking on the checkbox opens the content of the dump.

A button is provided to delete a dump.
