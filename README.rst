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

====
Tips
====

You can write your own generator, your implement the GeneratorInterface and change the default implementation in
your Objects.yaml (check the Objects.yaml from this package for the synthax)

==================
Identicons Samples
==================

.. image:: Documentation/Sample/dfeyer-donpark.png
	:alt: Don Park

.. image:: Documentation/Sample/dfeyer-githubstyle.png
	:alt: Github Style