<?php

namespace Weaverbird\Helpers;

class Metaboxes {

    protected $metaboxes = [];

    public function __construct() {
        $this->hooks();
    }

    public function hooks() {
        add_action( 'add_meta_boxes', [ $this, 'register' ], 10 );
    }

    public function register( $post_type ) {

        foreach ( $this->metaboxes as $metabox ) {
    
            $metabox = wp_parse_args( $metabox, [
                'id'        => '',
                'title'     => '',
                'post_type' => [ 'post' ],
                'fields'    => '',
            ] );
       
            $post_types = is_array( $metabox['post_type'] ) ? $metabox['post_type'] : [ $metabox['post_type'] ];

            if ( ! in_array( $post_type, $post_types ) ) {
                continue;
            }

            add_meta_box( $metabox['id'], $metabox['title'], [ $this, 'render' ], $post_types, 'normal', 'default', $metabox['fields'] );
        }
    }

    /**
     * Renders the metabox.
     */
    public function render( $post, $metabox ) {

        $fields = $metabox['args'] ?? [];
        
        ?>
            <table class="form-table">
                <tbody>
                    <?php foreach ( $fields as $field ) : ?>
                        <?php
                            // Get the field's value.
                            $field = wp_parse_args( $field, [
                                'type'  => 'text',
                                'id'    => '',
                                'name'  => '',
                                'label' => __( 'Label' ),
                                'value' => get_post_meta( get_the_ID(), $field['name'], true ),
                                'size'  => 30,
                            ] );
                            
                            $render_method = "render_{$field['type']}_field";

                            if ( method_exists( $this, $render_method ) ) {
                                $this->$render_method( $field );
                                continue;
                            }
                        ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php
    }

    /**
     * Renders a text field.
     */
    public function render_text_field( array $attr ) {
        ?>
            <tr>
                <td class="first"><label for="<?php echo esc_attr( $attr['id'] ); ?>"><?php echo esc_attr( $attr['label'] ); ?></label></td>
                <td><input type="text" name="meta_input[<?php echo esc_attr( $attr['name'] ); ?>]" size="<?php echo esc_attr( $attr['size'] ); ?>" value="<?php echo esc_attr( $attr['value'] ); ?>" id="<?php echo esc_attr( $attr['name'] ); ?>"></td>
            </tr>
        <?php
    }   
}