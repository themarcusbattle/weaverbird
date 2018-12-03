<?php

namespace Weaverbird;

class API {

    protected $cpts       = [];
    protected $taxonomies = [];

    public static function hooks( $object ) {

        // Load CPT hooks.
        foreach ( $object->cpts as $cpt ) {
            $cpt->hooks();
        }

        // Load Taxonomy hooks.
        foreach ( $object->taxonomies as $taxonomy ) {
            $taxonomy->hooks();
        }
    }
}