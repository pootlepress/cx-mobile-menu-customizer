<?php
/**
 * Created by Alan on 1/9/2014.
 */
if ( ! class_exists( 'MMM_Common_Control' ) ) :
    if (!class_exists( 'WP_Customize_Control' )) {
        require_once(ABSPATH . '/wp-includes/class-wp-customize-control.php');
    }
class MMM_Common_Control extends WP_Customize_Control {
    /**
     * Render the control's content.
     *
     * Allows the content to be overriden without having to rewrite the wrapper in $this->render().
     *
     * Supports basic input types `text`, `checkbox`, `radio`, `select` and `dropdown-pages`.
     *
     * @since 3.4.0
     */
    protected function render_content() {

        $default = $this->setting->default;

        switch( $this->type ) {
            case 'text':
                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <input type="text" data-default-value="<?php esc_attr_e($default) ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                </label>
                <?php
                break;
            case 'checkbox':
                ?>
                <label>
                    <input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?>
                    <?php
                    if ($default == '1' ) {
                        echo 'data-default-value="1"';
                    } else {
                        echo 'data-default-value="0"';
                    }
                    ?> />
                    <?php echo esc_html( $this->label ); ?>
                </label>
                <?php
                break;
            case 'radio':
                if ( empty( $this->choices ) )
                    return;

                $name = '_customize-radio-' . $this->id;

                ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php
                foreach ( $this->choices as $value => $label ) :
                    ?>
                    <label>
                        <input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
                        <?php echo esc_html( $label ); ?><br/>
                    </label>
                <?php
                endforeach;
                break;
            case 'select':
                if ( empty( $this->choices ) )
                    return;

                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <select <?php $this->link(); ?> data-default-value="<?php esc_attr_e($default) ?>" >
                        <?php
                        foreach ( $this->choices as $value => $label )
                            echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
                        ?>
                    </select>
                </label>
                <?php
                break;
            case 'dropdown-pages':
                $dropdown = wp_dropdown_pages(
                    array(
                        'name'              => '_customize-dropdown-pages-' . $this->id,
                        'echo'              => 0,
                        'show_option_none'  => __( '&mdash; Select &mdash;' ),
                        'option_none_value' => '0',
                        'selected'          => $this->value(),
                    )
                );

                // Hackily add in the data link parameter.
                $dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

                printf(
                    '<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
                    $this->label,
                    $dropdown
                );
                break;
        }
    }
}
endif;