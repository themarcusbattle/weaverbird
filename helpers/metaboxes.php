<?php

namespace Weaverbird\Helpers;

class Metaboxes {

    protected $metaboxes = [];

    public function init() {
        add_action( 'add_meta_boxes', [ $this, 'register' ] );
        add_action( 'wb_metabox_render_text_field', [ $this, 'render_text_field' ] );
    }

    public function register() {

        foreach ( $this->metaboxes as $metabox ) {

            $metabox = wp_parse_args( $metabox, [
                'id'        => '',
                'title'     => '',
                'post_type' => 'post',
                'fields'    => '',
            ] );

            add_meta_box( $metabox['id'], $metabox['title'], [ $this, 'render' ], $metabox['post_type'], 'normal', 'default', $metabox['fields'] );
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

                            do_action( "wb_metabox_render_{$field['type']}_field", $field ); 
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