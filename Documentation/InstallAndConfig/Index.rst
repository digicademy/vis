..  include:: /Includes.rst.txt

..  _install-and-config:

==================
Install and config
==================

..  rst-class:: bignums

1.  Install the extension

    Add the package to your ``composer.json`` via a PHP Composer command:

    ..  code-block::

        composer require digicademy/vis:^2

2.  Integrate styles

    Integrate the following minimal style into your CSS to make sure that visualisation containers are visible:

    ..  code-block:: css

        .mdlr-frame {
            min-height: 500px;
        }

    If you use the `MDLR <https://github.com/digicademy/mdlr>`__ extension, styles are provided automatically.
