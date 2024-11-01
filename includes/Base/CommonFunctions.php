<?php
/**
 * 
 * @package Shop2API
 *
 * This is common functions that is used alot and can be reused
 */

class Shop2API_CommonFunctions
{
    public static function expanded_alowed_tags() {
        $my_allowed = wp_kses_allowed_html( 'post' );
        // iframe
        $my_allowed['iframe'] = array(
            'src'             => array(),
            'height'          => array(),
            'width'           => array(),
            'frameborder'     => array(),
            'allowfullscreen' => array(),
        );
        // form fields - input
        $my_allowed['input'] = array(
            'class' => array(),
            'id'    => array(),
            'name'  => array(),
            'value' => array(),
            'type'  => array(),
        );
        // select
        $my_allowed['select'] = array(
            'class'  => array(),
            'id'     => array(),
            'name'   => array(),
            'value'  => array(),
            'type'   => array(),
            'data-bol_cat_slug' => array(),
        );
        // select options
        $my_allowed['option'] = array(
            'selected' => array(),
            'value' => array(),
        );
        // style
        $my_allowed['style'] = array(
            'types' => array(),
        );

        //Table Data with data 
        $my_allowed['td'] = array(
            'class'  => array(),
            'id'     => array(),
            'name'   => array(),
        );

        $my_allowed['tr'] = array(
            'class'  => array(),
            'id'     => array(),
            'name'   => array()
        );

        //Form
        $my_allowed['form'] = array(
            'class'  => array(),
            'id'     => array(),
            'name'   => array(),
            'method' => array(),
        );

        //Span
        $my_allowed['span'] = array(
            'class'  => array(),
            'id'     => array(),
            'name'   => array(),
            'title'   => array(),
            'data-wc_id' => array(),
            'data-wc_ean' => array(),
        );

        //img
        $my_allowed['img'] = array(
            'class'  => array(),
            'id'     => array(),
            'name'   => array(),
            'src'   => array(),
        );

        return $my_allowed;
    }
}
