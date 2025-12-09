<?php
declare(strict_types=1);

# This file is part of the VIS extension for TYPO3.
#
# For the full copyright and license information, please read the
# LICENSE.txt file that was distributed with this source code.


namespace Digicademy\VIS\ViewHelpers;

use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

defined('TYPO3') or die();

/**
 * ViewHelper to produce a map visualisation based on GeoJSON data
 */
final class MapViewHelper extends AbstractTagBasedViewHelper
{
    protected $tagName = 'div';

    private AssetCollector $assetCollector;

    public function __construct()
    {
        parent::__construct();
        $this->assetCollector = GeneralUtility::makeInstance(AssetCollector::class);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'id',
            'string',
            'ID of the map element',
            true,
        );
        $this->registerArgument(
            'class',
            'string',
            'CSS classes of the map element',
            false,
            'mdlr-frame mdlr-variant-map',
        );
        $this->registerArgument(
            'style',
            'string',
            'URL of the tile layer to use',
            false,
            'https://tiles.openfreemap.org/styles/positron',
        );
        $this->registerArgument(
            'centerLatitude',
            'float',
            'Latitude coordinate to use as the initial center of the map',
            false,
            8.2704513,
        );
        $this->registerArgument(
            'centerLongitude',
            'float',
            'Longitude coordinates to use as the initial center of the map',
            false,
            49.9765528,
        );
        $this->registerArgument(
            'zoom',
            'float',
            'Initial zoom factor of the map',
            false,
            9,
        );
        $this->registerArgument(
            'color',
            'string',
            'Color to apply to initial features',
            false,
            '#000'
        );
        $this->registerArgument(
            'markers',
            'bool',
            'Whether to show markers instead of drawing features',
            false,
            false,
        );
        $this->registerArgument(
            'geoJson',
            'string',
            'GeoJSON features to show on the map',
            false,
            '{}',
        );
    }

    public function render(): string
    {
        // Set variables from arguments
        $id = htmlspecialchars($this->arguments['id']);
        $class = htmlspecialchars($this->arguments['class']);
        $style = htmlspecialchars($this->arguments['style']);
        $centerLatitude = $this->arguments['centerLatitude'];
        $centerLongitude = $this->arguments['centerLongitude'];
        $zoom = $this->arguments['zoom'];
        $color = htmlspecialchars($this->arguments['color']);
        $markers = $this->arguments['markers'];
        $geoJson = json_encode(json_decode($this->arguments['geoJson'], true));

        // Compose map-specific JS code
        $map = "
            /* Base map */
            const {$id} = new maplibregl.Map({
                container: '{$id}',
                style: '{$style}',
                center: [{$centerLatitude}, {$centerLongitude}],
                zoom: {$zoom},
                pitch: 20,
                rollEnabled: true,
                attributionControl: false,
            });

            /* Projection */
            {$id}.on('style.load', () => {
                {$id}.setProjection({
                    type: 'globe',
                });
            });

            /* Attribution control */
            {$id}.addControl(new maplibregl.AttributionControl({
                compact: true,
            }), 'bottom-left');

            /* Fullscreen control */
            {$id}.addControl(new maplibregl.FullscreenControl(), 'bottom-right');

            /* Navigation controls */
            {$id}.addControl(new maplibregl.NavigationControl({
                visualizePitch: true,
                visualizeRoll: true,
                showZoom: true,
                showCompass: true,
            }), 'bottom-right');

            /* Generic function to add a marker */
            function visShowMarker(feature) {

                /* Add popup */
                let popupContent = '';
                if(feature.properties.url && feature.properties.name) {
                    popupContent = '<a href=\"' + feature.properties.url + '\">' + feature.properties.name + '</a>';
                } else if(feature.properties.name) {
                    popupContent = feature.properties.name;
                }
                let popup = new maplibregl.Popup()
                    .setHTML(popupContent);

                /* Add marker */
                let marker = new maplibregl.Marker({
                        color: '{$color}',
                    }).setLngLat(feature.geometry.coordinates)
                    .setPopup(popup)
                    .addTo({$id});

                /* Provide marker for list */
                return marker;
            }

            /* Generic function to add a feature */
            function visShowFeature(feature) {

                /* Add payload as source */
                {$id}.addSource('payload', {
                    'type': 'geojson',
                    'data': payload,
                });

                /* Render source: circles */
                {$id}.addLayer({
                    'id': 'payload',
                    'type': 'circle',
                    'source': 'payload',
                    'paint': {
                        'circle-radius': 6,
                        'circle-color': '#B42222',
                    },
                    'filter': ['==', '\$type', 'Point']
                });

                /* Render source */
                /*{$id}.addLayer({
                    'id': 'payload',
                    'type': 'fill',
                    'source': 'payload',
                    'paint': {
                        'fill-color': '#888888',
                        'fill-outline-color': 'red',
                        'fill-opacity': 0.4,
                    },
                    // filter for (multi)polygons; for also displaying linestrings
                    // or points add more layers with different filters
                    'filter': ['==', '\$type', 'Polygon']
                });*/
            }
        ";

        // Either render markers from GeoJSON
        if($markers) {
            $map .= "
                /* GeoJSON payload */
                const payload = {$geoJson};
                let markers = [];
                {$id}.on('load', () => {

                    /* Show individual or multiple markers */
                    if(payload.geometry) {
                        markers.push(visShowMarker(payload));
                    } else if(payload.features) {
                        payload.features.forEach((feature) => {
                            markers.push(visShowMarker(feature));
                        });
                    }
                });
            ";

        // Or render GeoJSON features
        } else {
            $map .= "
                /* GeoJSON payload */
                const payload = {$geoJson};
                {$id}.on('load', () => {

                    /* Show individual or multiple features */
                    visShowFeature(payload);
                });
            ";
        }

        // Include MapLibre GL JS assets
        $this->assetCollector->addJavaScript(
            'maplibre-gl-js',
            'EXT:vis/Resources/Public/JavaScript/maplibre-gl.js',
            [],
            ['priority' => true],
        );
        $this->assetCollector->addStyleSheet(
            'maplibre-gl-css',
            'EXT:vis/Resources/Public/Css/maplibre-gl.css',
            ['media' => 'all'],
        );

        // Include map-specific JS code
        $this->assetCollector->addInlineJavaScript(
            'maplibre-gl-js',
            $map,
        );

        // Create tag
        $this->tag->forceClosingTag(true);
        $this->tag->addAttribute(
            'id',
            $id,
        );
        $this->tag->addAttribute(
            'class',
            $class,
        );

        // Render tag
        return $this->tag->render();

        // TODO Finish GeoJSON rendering
        // TODO Fix button und popover styles, maybe icons
        // TODO Zoom to features?
    }
}
