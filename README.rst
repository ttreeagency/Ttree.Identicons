**************************************************
Neos Flow Framework Package to generate Identicons
**************************************************

This contains the Flow Framework package "Ttree.Identicons" to generate Identicons. Currently only two generators are
available: Don Park (original version) and Github Style (inspired by the space invader style identicons provided by
github).

============
Installation
============

1. Just install and activate the package

2. Include the package subroutes in your main Routes.yaml (optional: if you use only the fluid ViewHelper)

::

	-
	  name: 'Identicons'
	  uriPattern: 'i/<IdenticonsSubroutes>'
	  subRoutes:
		IdenticonsSubroutes:
		  package: Ttree.Identicons

If you change the ```uriPattern```, please read the section **Flood Mitigation**.

3. Configure Imagine to use the ```Imagick``` drive. *GD driver generate bad artifacts*.

4. Go to www.yourdomain/i/[yourhash].png (replace [yourhash] by any string)

This package is available on Packagist.org: https://packagist.org/packages/ttree/identicons

========
Settings
========

+--------------------+----------------------------------------+-------------------------+
| Setting            | Description                            | Default Value           |
+====================+========================================+=========================+
| persist            | Enable persistance                     | TRUE                    |
+--------------------+----------------------------------------+-------------------------+
| size               | Default size (h/w) of the square icon  | 420                     |
+--------------------+----------------------------------------+-------------------------+
| backgroundColor    | Default background color               | #EEE                    |
+--------------------+----------------------------------------+-------------------------+
| ttl                | HTTP Cache header TTL in seconds       | 2592000                 |
+--------------------+----------------------------------------+-------------------------+
| size               | Size constraints                       | 32 / 2048               |
+--------------------+----------------------------------------+-------------------------+
| flood.enable       | Activate Flood mitigation              | TRUE                    |
+--------------------+----------------------------------------+-------------------------+
| flood.limit        | Maximum number of request per minute   | 30                      |
+--------------------+----------------------------------------+-------------------------+
| access.enable      | Enable advanced access limitation      | FALSE                   |
+--------------------+----------------------------------------+-------------------------+

=================
Request Arguments
=================

- ```s```: The size of the image, between 32 and 2048 pixels, (default: 420px)
- ```b```: The background color (default: transparent)

================
Flood Mitigation
================

By default this package limit the request rate per minute (for a single IP address) to 30
requests. You can change this in Settings.yaml. The flood mitigation use the caching
framework to store request rate statistics, please change the default FileBackend for
a production use.

**Important**: When an IP address is blocked, for a maximum of 1 minute, the Application Firewall
block the request early in the Flow bootstrap (when a request arrive at the MVC dispatcher).
If you don't use the default URL www.domain/i/hash.png, you need to change the patternValue
in Settings.yaml, in the Flow Application Firewall section.

==========================
Advanced access limitation
==========================

By default this package will generate an identicons for any hash, if you need to limit this,
per ex. you need to generate identicon only for existing Party, you can implement the interface
AccessValidationInterface and enable access validation in the settings.

You also need to change the default implementation for this interface in your Objects.yaml
(check the Objects.yaml from this package for the syntax).

=================
Fluid ViewHelpers
=================

You can insert an identicon in your Fluid template by using the provided ImageViewHelper.

::

	{namespace identicon=Ttree\Identicons\ViewHelpers}
	<identicon:image hash="ttree" alt="ttree -- identicon" size="42" class="img-polaroid" />

If the identicon doesn't exist for the provided hash, it will be created and persisted automatically.

**Warning**: Advanced Access Limitation and Flood Mitigation are not supported by the Fluid ViewHelper,
you need to take care of your server by yourself.

====
Tips
====

You can write your own generator, just implement the GeneratorInterface and change the default implementation in
your Objects.yaml (check the Objects.yaml from this package for the syntax).

**Warning**: if you change the Generator, currently you need to truncate the table "ttree_identicons_domain_model_identicon"
manually.

==================
Identicons Samples
==================

.. figure:: Documentation/Sample/dfeyer-donpark.png
	:alt: Don Park

.. figure:: Documentation/Sample/ttree-githubstyle.png
	:alt: Github Style
