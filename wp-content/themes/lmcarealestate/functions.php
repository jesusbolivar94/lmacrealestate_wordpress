<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


// Enqueue parent theme styles
function astra_child_enqueue_styles()
{
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('astra-child-style', get_stylesheet_directory_uri() . '/style.css', array('astra-parent-style'), wp_get_theme()->get('Version'));
}

add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles');

/**
 * Display sorting dropdown.
 *
 * @param $sort
 * @return void
 */
function es_sort_dropdown_custom( $sort ): void
{
    $sorting = ests( 'properties_sorting_options' );

    if ( ! empty( $sorting ) ) : ?>
        <div class="es-form">
            <?php es_framework_field_render( 'sort', array(
                'type' => 'select',
                'value' => $sort,
                'options' => ests_selected( 'properties_sorting_options' ),
                'label' => __( 'Sort by', 'es' ),
                'attributes' => array(
                    'class' => 'js-es-sort es-custom-sort'
                )
            ) ); ?>
        </div>
    <?php endif;
}
add_action( 'es_sort_dropdown_custom', 'es_sort_dropdown_custom' );

/**
 * Render advanced search field.
 *
 * @param $field
 * @param array $attributes
 * @param null $force_type
 */
function es_search_render_field_custom( $field, $attributes = array(), $force_type = null ) {
    $field_config = es_search_get_field_config( $field );
    if ( $field_config && ! empty( $field_config['search_support'] ) ) {
        $search_settings = $field_config['search_settings'];
        $type = $force_type ? $force_type : $search_settings['type'];
        $uid = uniqid();
        $selected_value = isset( $attributes[ $field ] ) ? $attributes[ $field ] : null;
        $selected_value = isset( $_GET[ $field ] ) ? es_clean( $_GET[ $field ] ) : $selected_value;

        if ( empty( $search_settings['values'] ) && ! empty( $search_settings['values_callback'] ) ) {
            if ( ! empty( $search_settings['values_callback']['args'] ) ) {
                $values = call_user_func_array( $search_settings['values_callback']['callback'], $search_settings['values_callback']['args'] );
            } else {
                $values = call_user_func( $search_settings['values_callback']['callback'] );
            }

            if ( $values && ! is_wp_error( $values ) ) {
                $search_settings['values'] = $values;
            }
        }

        $field_html = null;

        if ( !empty ( $field_config['frontend_visible_name'] ) ) {
            $label = es_mulultilingual_translate_string( $field_config['frontend_visible_name'] );
        } else {
            $label = $field_config['label'];
        }

        switch ( $type ) {
            case 'price':
                $values = array();
                if (ests('price_input_type') != 'manual_input') {
                    if ( ests( 'is_same_price_for_categories_enabled' ) ) {
                        $values['min'] = ests( 'min_prices_list' ) ? explode( ',', ests( 'min_prices_list' ) ) : array();
                        $values['max'] = ests( 'max_prices_list' ) ? explode( ',', ests( 'max_prices_list' ) ) : array();

                        $values['min'] = array_combine( $values['min'], $values['min'] );
                        $values['max'] = array_combine( $values['max'], $values['max'] );

                        $prices_list = array();
                    } else {
                        if ( $prices_list = ests( 'custom_prices_list' ) ) {
                            $formatter = $field_config['formatter'];
                            foreach ( $prices_list as $k => $price_item ) {
                                if ( empty( $price_item['type'] ) && empty( $price_item['category'] ) ) {
                                    $values['min'] = explode( ',', $price_item['min_prices_list'] );
                                    $values['max'] = explode( ',', $price_item['max_prices_list'] );
                                }

                                $min_values = explode( ',', $price_item['min_prices_list'] );
                                $max_values = explode( ',', $price_item['max_prices_list'] );

                                if ( ! empty( $min_values ) ) {
                                    $prices_list[ $k ]['min_prices_list'] = array_combine( $min_values, es_format_values( $min_values, $formatter ) );
                                }

                                if ( ! empty( $max_values ) ) {
                                    $prices_list[ $k ]['max_prices_list'] = array_combine( $max_values, es_format_values( $max_values, $formatter ) );
                                }
                            }
                        }
                    }

                    $field_html = "<div class='es-field-row es-field-row__range js-search-field-container'>";

                    foreach ( array( 'min', 'max' ) as $field_range ) {
                        if ( ! empty( $values[ $field_range ] ) ) {
                            $values[ $field_range ] = array_combine( $values[ $field_range ], es_format_values( $values[ $field_range ], $field_config['formatter'] ) );
                        }
                        $range_label = ! empty( $search_settings['range_label'] ) ? $search_settings['range_label'] : $label;
                        $field_name = $field_range . '_' . $field;
                        $value = isset( $attributes[ $field_name ] ) ? $attributes[ $field_name ] : null;
                        $value = isset( $_GET[ $field_name ] ) ? es_clean( $_GET[ $field_name ] ) : $value;

                        $config = array(
                            'type' => ! empty( $values[ $field_range ] ) ? 'select' : 'number',
                            'label' => $field_range == 'min' ? $range_label : false,
                            'value' => $value,
                            'attributes' => array(
                                'data-prices-list' => es_esc_json_attr( $prices_list ),
                                'id' => sprintf( '%s-%s-%s', $field, $field_range, $uid ),
                                'class' => 'js-es-search-field js-es-search-field--price ' . sprintf( 'js-es-search-field--price-%s', $field_range ),
                                'data-base-name' => $field,
                                'data-placeholder' => $field_range == 'min' ? __( 'Preço Min.', 'es' ) : __( 'Preço Max.', 'es' ),
                                'placeholder' => $field_range == 'min' ? __( 'Min', 'es' ) : __( 'Max', 'es' ),
                            ),
                            'options' => ! empty( $values[ $field_range ] ) ? array( '' => '' ) + $values[ $field_range ] : array(),
                        );

                        $field_html .= es_framework_get_field_html( $field_name, es_parse_args( $config, $search_settings ) );
                    }
                    $field_html .= "</div>";
                }
                if (ests('price_input_type') != 'manual_input') {
                    $search_settings['range'] = false;
                }
                break;
            case 'select':
            case 'list':
            case 'dropdown':
                $search_settings['values'] = es_format_values( $search_settings['values'], $field_config['formatter'] );
                $values = $search_settings['values'];

                if ( ! empty( $search_settings['attributes']['data-placeholder'] ) ) {
                    $values = array( '' => '' ) + $values;
                }

                if ( 'keywords' == $field && $selected_value ) {
                    $values = array_combine( $selected_value, $selected_value );
                }

//                    if ( ! $search_settings['attributes']['multiple'] ) {
//                        $values = array( '' => _x( 'All', 'search dropdown placeholder', 'es' ) ) + $values;
//                    }

                $config = array(
                    'type'       => $type,
                    'options'    => $values,
                    'value' => $selected_value,
                    'attributes' => array(
                        'id' => sprintf( '%s-%s', $field, $uid ),
                        'class' => sprintf( 'js-es-search-field js-es-search-field--%s', $field ),
                        'data-base-name' => $field,
                    ),
                    'label' => ! empty( $field_config['label'] ) ? $label : '',
                );

                if ( ! empty( $selected_value ) ) {
                    if ( is_scalar( $selected_value ) ) {
                        $config['attributes']['data-value'] = $selected_value;
                    } else if ( is_array( $selected_value ) ) {
                        $config['attributes']['data-value'] = es_esc_json_attr( $selected_value );
                    }
                }

                $search_settings['wrapper_class'] .= ' js-search-field-container';
                $field_html = es_framework_get_field_html( $field, es_parse_args( $config, $search_settings ) );
                break;

            case 'checkboxes':
                if ( ! empty( $search_settings['values'] ) ) {
                    $values = es_format_values( $search_settings['values'], $field_config['formatter'] );
                    $visible_items = ! empty( $search_settings['visible_items'] ) ? $search_settings['visible_items'] : false;

                    $config = array(
                        'type'       => $type,
                        'options'    => $values,
                        'disable_hidden_input' => true,
                        'value' => $selected_value,
                        'visible_items' => $visible_items,
                        'button_label' => ! empty( $search_settings['show_more_label'] ) ? $search_settings['show_more_label'] : '',
                        'attributes' => array(
                            'id' => sprintf( '%s-%s', $field, $uid ),
                            'class' => sprintf( 'js-es-search-field js-es-search-field--%s', $field ),
                            'data-base-name' => $field,
                        ),
                        'label'      => $label,
                    );

                    $search_settings['wrapper_class'] .= ' js-search-field-container';
                    $field_html = es_framework_get_field_html( $field, es_parse_args( $config, $search_settings ) );
                }
                break;

            case 'radio-bordered':
            case 'checkboxes-bordered':
            case 'checkboxes-boxed':
                if ( ! empty( $search_settings['values'] ) ) {
                    $options = $search_settings['values'];
                    $field_name = $field;
                    $field_class = sprintf( 'js-es-search-field js-es-search-field--%s', $field_name );

                    if ( in_array( $field, array( 'bedrooms', 'bathrooms', 'half_baths' ) ) ) {
                        array_walk( $search_settings['values'], 'es_arr_add_suffix_plus' );
                        $options = array( '' => __( 'Any', 'es' ) ) + $search_settings['values'];
                        $field_name = 'from_' . $field;
                        $selected_value = isset( $attributes[ $field_name ] ) ? $attributes[ $field_name ] : null;
                        $selected_value = isset( $_GET[ $field_name ] ) ? es_clean( $_GET[ $field_name ] ) : $selected_value;
                    }

                    $config = array(
                        'type' => $type,
                        'options' => $options,
                        'label' => $label,
                        'value' => $selected_value,
//                            'disable_hidden_input' => true,
                        'attributes' => array(
                            'id' => sprintf( '%s-%s', $field_name, $uid ),
                            'class' => $field_class,
                            'data-formatter' => $field_config['formatter'],
                            'data-base-name' => $field,
                        ),
                    );

                    $search_settings['wrapper_class'] .= ' js-search-field-container';
                    $field_html = es_framework_get_field_html( $field_name, es_parse_args( $config, $search_settings ) );
                }
                break;

            case 'range':
                $field_html = "<div class='es-field-row es-field-row__range js-search-field-container'>";
                foreach ( array( 'min', 'max' ) as $field_range ) {
                    $range_label = ! empty( $search_settings['range_label'] ) ? $search_settings['range_label'] : $label;
                    $values = ! empty( $search_settings['values_' . $field_range] ) ? $search_settings['values_' . $field_range] : array();
                    $values = es_format_values( $values, $field_config['formatter'] );
                    $field_name = $field_range . '_' . $field;
                    $selected_value = isset( $attributes[ $field_name ] ) ? $attributes[ $field_name ] : null;
                    $selected_value = isset( $_GET[ $field_name ] ) ? es_clean( $_GET[ $field_name ] ) : $selected_value;
                    $config = array(
                        'type' => $values ? 'select' : 'number',
                        'label' => $field_range == 'min' ? $range_label : false,
                        'value' => $selected_value,
                        'attributes' => array(
                            'id' => sprintf( '%s-%s-%s', $field, $field_range, $uid ),
                            'min' => ests( 'search_min_' . $field ),
                            'max' => ests( 'search_max_' . $field ),
                            'data-formatter' => $field_config['formatter'],
                            'class' => sprintf( 'js-es-search-field js-es-search-field--%s', $field ),
                            'data-base-name' => $field,
                            'data-placeholder' => $field_range == 'min' ? __( 'No min', 'es' ) : __( 'No max', 'es' ),
                            'placeholder' => $field_range == 'min' ? __( 'No min', 'es' ) : __( 'No max', 'es' ),
                        ),
                        'options' => array( '' => '' ) + $values,
                    );

                    $field_html .= es_framework_get_field_html( $field_name, es_parse_args( $config, $search_settings ) );
                }
                $field_html .= "</div>";

                break;
            default:
                $search_settings['wrapper_class'] .= ' js-search-field-container';
                $field_config = es_array_merge_recursive( $field_config, $search_settings );
                $field_config['value'] = $selected_value;
                $field_html = es_framework_get_field_html( $field, $field_config );
        }

        if ( ! empty( $field_html ) || ( ! empty( $attributes['type'] ) && $attributes['type'] == 'range' ) ) {
            echo apply_filters( 'es_search_render_field_html', $field_html, $field, $attributes, $force_type );
        }

        if ( ! empty( $search_settings['range'] ) && $type != 'range' ) {
            $field_config['type'] = 'range';
            $field_config['search_settings']['type'] = 'range';
            es_search_render_field_custom( $field, $field_config, 'range' );
        }
    }
}
add_action( 'es_search_render_field_custom', 'es_search_render_field_custom', 10, 2 );

remove_action( 'es_after_listings', 'es_powered_by' );
remove_action( 'es_after_single_content', 'es_powered_by' );
remove_action( 'es_after_authentication', 'es_powered_by' );
remove_action( 'es_after_profile', 'es_powered_by' );
