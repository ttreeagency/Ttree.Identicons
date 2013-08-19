***********************************
FLOW Package to generate Identicons
***********************************

This contains the TYPO3 Flow package "Ttree.Identicons" to generate Identicons. Currently only two generators are
available: Don Park (original version) and Github Style (inspired by the space invader style identicons provided by
github).

============
Installation
============

1. Just install and activate the package

2. Include the package subroutes in your main Routes.yaml

::

	-
	  name: 'Identicons'
	  uriPattern: 'i/<IdenticonsSubroutes>'
	  subRoutes:
		IdenticonsSubroutes:
		  package: Ttree.Identicons

3. Go to www.yourdomain/i/[yourhash].png (replace [yourhash] by any string)

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
| cacheControl       | Default Cache Control header           | max-age=2592000, public |
+--------------------+----------------------------------------+-------------------------+


=================
Fluid ViewHelpers
=================

You can insert an identicon in your Fluid template by using the provided ImageViewHelper.

::

	{namespace identicon=Ttree\Identicons\ViewHelpers}
	<identicon:image hash="ttree" alt="ttree -- identicon" size="42" class="img-polaroid" />

If the identicon doesn't exist for the provided hash, it will be created and persisted automatically.

====
Tips
====

You can write your own generator, just implement the GeneratorInterface and change the default implementation in
your Objects.yaml (check the Objects.yaml from this package for the synthax).

**Warning**: if you change the Generator, currently you need to truncate the table "ttree_identicons_domain_model_identicon"
manually. This will change in a future release.

==================
Identicons Samples
==================

.. figure:: Documentation/Sample/dfeyer-donpark.png
	:alt: Don Park

	the Don Park style identicon for "dfeyer"

.. figure:: Documentation/Sample/ttree-githubstyle.png
	:alt: Github Style

	the Github style identicon for "ttree"