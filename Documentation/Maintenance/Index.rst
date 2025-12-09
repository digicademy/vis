..  include:: /Includes.rst.txt

..  _maintenance:

===========
Maintenance
===========

The following information is primarily **intended for the further development**
of this package. Maintenance of the extension may include adapting it to new
TYPO3 versions or updating dependencies/includes.

..  _updating-dependencies:

Updating dependencies
=====================

The extension bundles a few frontend dependencies as includes that need
periodical updates when new releases are available. The process is not
automated yet. Updating a dependency means placing the new version of JS and
CSS assets in their respective folders along with their license files. In
major releases, all dependent ViewHelpers need to be checked.

* MapLibre GL JS (JS and CSS file, currently v.5.14.0)

..  _creating-a-new-release:

Creating a new release
======================

1. Commit all changes to the repo's main branch.
2. Add the new version number and release date to :file:`guides.xml` and :file:`CITATION.cff`.
3. Create a new release with the new version number, e.g. `v1.0.0`.
