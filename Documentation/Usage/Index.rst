..  include:: /Includes.rst.txt

..  _using-the-visualisations:

========================
Using the visualisations
========================

There are two ways to use the visualisations provided by this extension: as
plugins that can be used like content elements in the backend, and as
ViewHelpers to use in Fluid templates. Both need to be equipped with data
they should render.

..  _plugins-as-content-elements:

Plugins as content elements
===========================

When you add a new content element in the TYPO3 backend, VIS provides a new
:guilabel:`Visualisations` tab. It allows you to select all currently available
types of visualisations. Each VIS content element allows you to provide data in
a given format that is then rendered on the page.

..  _viewhelpers-in-templates:

ViewHelpers in templates
========================

There is also a ViewHelper to use in your Fluid template files for each
available type of visualisation. Adding a map to a template, for example, may
be as easy as adding the following template code:

..  code-block:: html

    <f:variable name="positionData">
        {
            "type": "Feature",
            "geometry": {
                "type": "Point",
                "coordinates": [8.2704513, 49.9765528]
            },
            "properties": {
                "name": "Academy of Sciences and Literature Mainz",
                "url": "https://www.adwmainz.de"
            }
        }
    </f:variable>
    <vis:map id="position" centerLatitude="8.2704513" centerLongitude="49.9765528" zoom="13" markers="{true}" geoJson="{positionData}" />

VIS takes care of CSS/JS assets necessary to render the visualisation.
