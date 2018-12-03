<?php

namespace Weaverbird\Helpers;

class Taxonomy {

    protected $name     = '';
    protected $objects  = [];
    protected $settings = [];

    public function hooks() {
        add_action( 'init', [ $this, 'register' ] );
        add_action( "{$this->name}_add_form_fields", [ $this, 'add_form_fields' ], 10 );
        add_action( "{$this->name}_edit_form_fields", [ $this, 'edit_form_fields' ], 10 );
        add_action( "create_{$this->name}", [ $this, 'save_form_fields_meta' ], 10 );
        add_action( "edited_{$this->name}", [ $this, 'save_form_fields_meta' ], 10 );
        add_filter( 'parent_file', [ $this, 'fix_taxonomy_page_for_users' ], 10 );
    }

    public static function register() {
        register_taxonomy( $this->name, $this->objects, $this->settings );
    }

    public function add_form_fields( string $taxonomy ) {
        return false;
    }

    public function edit_form_fields( \WP_Term $one ) {
        return false;
    }

    public function save_form_fields_meta( int $term_ID ) {

        $term_meta = $_POST['term_meta'] ?? false;

        if ( ! $term_meta ) {
            return;
        }

        foreach ( $term_meta as $meta_key => $meta_value ) {
            update_term_meta( $term_ID, $meta_key, $meta_value );
        }
    }

    public function fix_taxonomy_page_for_users( string $parent_file ) {

        if ( ! in_array( 'user', $this->objects ) ) {
            return $parent_file;
        }
        
        global $pagenow;

        $taxonomy  = $_GET['taxonomy'] ?? '';
        $post_type = $_GET['post_type'] ?? false;
        
        if ( ! empty( $_GET[ 'taxonomy' ] ) && $_GET[ 'taxonomy' ] == 'college' && $pagenow == 'edit-tags.php' && ! $post_type ) {
            $parent_file = 'users.php';
        }

        return $parent_file;
    }
}