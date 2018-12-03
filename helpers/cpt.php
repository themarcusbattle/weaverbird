<?php

namespace Weaverbird\Helpers;

class CPT {

    protected $name        = '';
    protected $settings    = [];
    protected $slug_is_int = false;

    public function hooks() {

        add_action( 'init', [ $this, 'init_post_type' ] );
        add_action( 'save_post', [ $this, 'change_slug_to_ID' ] );
        add_action( 'pre_get_posts', [ $this, 'add_post_types_to_main_query' ] );
    }

    /**
     * Register the custom post type.
     */
    public function init_post_type() {
        $status = register_post_type( $this->name, $this->settings );
    }

    /**
     * Changes the post type slugs to IDs.
     */
    public function change_slug_to_ID( int $post_ID ) {

        if ( wp_is_post_revision( $post_ID ) ) {
            return;
        }

        if ( ! $this->slug_is_int ) {
            return;
        }

        // Unhook this function so it doesn't loop infinitely.
		remove_action( 'save_post', [ $this, 'change_slug_to_ID' ] );

		// Update the post, which calls save_post again.
		wp_update_post( [
            'ID'        => $post_ID,
            'post_name' => $post_ID,
        ] );

		// Re-hook this function.
		add_action( 'save_post', [ $this, 'change_slug_to_ID' ] );
    }

    /**
     * Adds the custom post types to the main query.
     */
    public function add_post_types_to_main_query( \WP_Query $query ) {

        if ( ! is_admin() && $query->is_main_query() && is_home() ) {

            $custom_post_types = get_post_types( [
                'public'   => true,
                '_builtin' => false,
            ] );

            $query->set( 'post_type', $custom_post_types );
        }

        return $query;
    }

    public function get( $count = 10 ) {

        if ( $count === 'all' ) {
            $count = '-1';
        }

        $args = [
            'post_type'      => $this->name,
            'posts_per_page' => $count,
        ];

        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) {
            return $query->posts;
        }

        return false;
    }
}
