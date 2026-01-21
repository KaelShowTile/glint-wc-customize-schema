<?php
/**
 * Schema Frontend Class
 *
 * Generates and outputs schema markup in the frontend header
 */

class Schema_Frontend {

    //Initialize the class
    public static function init() {
        add_action( 'wp_head', array( __CLASS__, 'output_schema_markup' ) );
        add_action( 'wp_head', array( __CLASS__, 'disable_yoast_schema_if_needed' ), 0 );
    }

    //Disable Yoast SEO schema and WC default schema if custom schema is present
    public static function disable_yoast_schema_if_needed() {
        if ( defined( 'WPSEO_VERSION' ) && ! empty( self::get_schema_data_for_current_page() ) ) {
            add_filter( 'wpseo_json_ld_output', '__return_false' );
            add_filter( 'woocommerce_structured_data_product', '__return_false' );
        }
    }

    //Output schema markup in header
    public static function output_schema_markup() {
        $schema_data = self::get_schema_data_for_current_page();

        if ( ! empty( $schema_data ) ) {
            echo '<script type="application/ld+json">' . wp_json_encode( $schema_data, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }
    }

    //Get schema data for current page
    private static function get_schema_data_for_current_page() {
        $schema_data = array();

        if ( is_singular( 'post' ) ) {
            $schema_data = self::get_schema_for_post_type( 'blog_post' );
        } elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
            $schema_data = self::get_schema_for_post_type( 'product_archive' );
        } elseif ( function_exists( 'is_product' ) && is_product() ) {
            $schema_data = self::get_schema_for_post_type( 'product_post' );
        } elseif ( is_archive() && ! is_post_type_archive() ) {
            $schema_data = self::get_schema_for_post_type( 'blog_archive' );
        } elseif ( is_page() ) {
            $schema_data = self::get_schema_for_page();
        } elseif ( is_post_type_archive() ) {
            $post_type = get_query_var( 'post_type' );
            $schema_data = self::get_schema_for_post_type( 'custom_post_archive', $post_type );
        } elseif ( is_singular() ) {
            $post_type = get_post_type();
            if ( $post_type !== 'post' && $post_type !== 'page' && ! ( function_exists( 'is_product' ) && $post_type === 'product' ) ) {
                $schema_data = self::get_schema_for_post_type( 'custom_post', $post_type );
            }
        }

        return $schema_data;
    }

    //Get schema for specific post type
    private static function get_schema_for_post_type( $page_type, $custom_post_type = '' ) {
        $args = array(
            'post_type' => 'schema_setting',
            'meta_query' => array(
                array(
                    'key' => '_schema_page_type',
                    'value' => $page_type,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        );

        if ( in_array( $page_type, array( 'custom_post', 'custom_post_archive' ) ) && ! empty( $custom_post_type ) ) {
            $args['meta_query'][] = array(
                'key' => '_schema_custom_post_slug',
                'value' => $custom_post_type,
                'compare' => '='
            );
        }

        $schema_posts = get_posts( $args );

        if ( empty( $schema_posts ) ) {
            return array();
        }

        $schema_data = array();
        foreach ( $schema_posts as $schema_post ) {
            $markups = get_post_meta( $schema_post->ID, '_schema_markups', true );
            if ( is_array( $markups ) ) {
                foreach ( $markups as $markup ) {
                    $schema_item = self::build_schema_item( $markup );
                    if ( $schema_item ) {
                        $schema_data[] = $schema_item;
                    }
                }
            }
        }

        return $schema_data;
    }

    //Get schema for page
    private static function get_schema_for_page() {
        global $post;

        $template_id = get_post_meta( $post->ID, '_page_schema_template', true );

        if ( empty( $template_id ) ) {
            return array();
        }

        $markups = get_post_meta( $template_id, '_schema_markups', true );

        if ( ! is_array( $markups ) ) {
            return array();
        }

        $schema_data = array();
        foreach ( $markups as $markup ) {
            $schema_item = self::build_schema_item( $markup );
            if ( $schema_item ) {
                $schema_data[] = $schema_item;
            }
        }

        return $schema_data;
    }

    //Build schema item from markup data
    private static function build_schema_item( $markup ) {
        if ( empty( $markup['type'] ) || empty( $markup['properties'] ) ) {
            return false;
        }

        $schema_item = array(
            '@context' => 'https://schema.org',
            '@type' => self::get_schema_type( $markup['type'] )
        );

        foreach ( $markup['properties'] as $key => $value ) {
            if ( ! empty( $value ) ) {
                $processed_value = self::process_property_value( $value );
                if ( $processed_value !== null ) {
                    $schema_item[$key] = $processed_value;
                }
            }
        }

        return $schema_item;
    }

    //Get schema.org type from our internal type
    private static function get_schema_type( $type ) {
        $type_map = array(
            'webpage' => 'WebPage',
            'article' => 'Article',
            'breadcrumb' => 'BreadcrumbList',
            'faq' => 'FAQPage',
            'local_business' => 'LocalBusiness',
            'image_metadata' => 'ImageObject',
            'product' => 'Product',
            'review_snippet' => 'Review',
            'video' => 'VideoObject',
            'organization' => 'Organization',
            'potential_action'=> 'BuyAction',
            'main_entity_of_page'=> 'WebPage',
            'item_page'=> 'ItemPage',
            'offer_schema'=> 'Offer',
            'search_action'=> 'WebSite',
            'profile'=> 'ProfilePage',
            'blog_posting'=> 'BlogPosting'
        );

        return isset( $type_map[$type] ) ? $type_map[$type] : ucfirst( $type );
    }

    //Process property value (handle dynamic values)
    private static function process_property_value( $value ) {
        // Handle dynamic values like $post->post_title
        if ( strpos( $value, '$' ) === 0 ) {
            return self::evaluate_dynamic_value( $value );
        }

        // Handle JSON values - parse JSON strings for specific properties
        $json_properties = array( 'itemListElement', 'mainEntity', 'offers', 'aggregateRating' );
        foreach ( $json_properties as $prop ) {
            if ( $value === $prop || strpos( $value, '{' ) === 0 || strpos( $value, '[' ) === 0 ) {
                $decoded = json_decode( $value, true );
                if ( json_last_error() === JSON_ERROR_NONE ) {
                    return $decoded;
                }
                // If it's not valid JSON, return as string
                break;
            }
        }

        return $value;
    }

    //Evaluate dynamic value
    private static function evaluate_dynamic_value( $value ) {
        global $post;
        global $wp_query;

        // Remove the $ and split by ->
        $parts = explode( '->', substr( $value, 1 ) );

        if ( empty( $parts ) ) {
            return null;
        }

        $object = $parts[0];
        $property = isset( $parts[1] ) ? $parts[1] : '';

        switch ( $object ) {
            case 'post':
                if ( $post ) {
                    switch ( $property ){
                        case 'get_title':
                            return $post->post_title;
                        case 'get_excerpt':
                            return $post->post_excerpt;
                        case 'get_permalink':
                            return get_the_permalink($post);
                        case 'post_date':
                            return $post->post_date;
                        case 'post_modified';
                            return $post->post_modified;
                        case 'get_thumb_url':
                            return get_the_post_thumbnail_url($post->ID);
                        case 'hasPart':
                            $has_part = array();
                            $has_part[] = array(
                                '@type' => 'Article',
                                'headline' => $post->post_title,
                                'url' => get_the_post_thumbnail_url($post->ID),
                                'datePublished' => $post->post_date,
                                'author' => '{ "@id": "#main-author" }'
                            );
                            return $has_part;
                        case 'mainEntityOfPage':
                            $main_entity = array();
                            $main_entity[] = array(
                                "@type" => "WebPage",
                                "@id" => get_the_permalink($post)
                            );
                            return $main_entity;
                    }       
                }
                
                break;
            case 'product':
                if ( function_exists( 'wc_get_product' ) && $post ) {
                    $product = wc_get_product( $post->ID );
                    if ( $product ) {
                        switch ( $property ) {
                            case 'get_title()':
                                return $product->get_title();
                            case 'get_id()':
                                $productID = "cht" . $post->ID;
                                return $productID;
                            case 'schema_id':
                                $schemaID = get_permalink( $product->get_id() ) . '#' . $post->ID;
                                return  $schemaID;
                            case 'get_description()':
                                $full_description = $product->get_description();
                                $schema_description = wp_trim_words( $full_description, 99999, '' );
                                return $schema_description;
                            case 'get_price()':
                                return $product->get_price();
                            case 'get_permalink()':
                                return get_permalink( $product->get_id() );
                            case 'get_thumb_url()':
                                return get_the_post_thumbnail_url($product->get_id());
                            case 'get_date_modified()':
                                return $product->get_date_modified() ? $product->get_date_modified()->format( 'c' ) : '';
                            case 'get_date_created()':
                                return $product->get_date_created() ? $product->get_date_created()->format( 'c' ) : '';
                            case 'get_status()':
                                return $product->get_status();
                            case 'get_stock_status()':
                                return $product->get_stock_status();
                            case 'get_price()':
                                $price = $product->get_sale_price();
                                if(!$price){
                                    $price = $product->get_regular_price();
                                }
                                return $price;
                            case 'get_seller()':
                                $site_name = get_bloginfo('name');
                                $site_url = get_site_url();
                                $seller[] = array(
                                    '@type' => 'Organization',
                                    'name' => $site_name,
                                    'url' => $site_url
                                );
                                return $seller;
                            case 'get_offers()':
                                $price = $product->get_sale_price();
                                $regularPrice = 0;
                                $availability = $product->get_stock_status();
                                if(!$price){
                                    $price = $product->get_regular_price();
                                    $offers[] = array(
                                        '@type' => 'Offer',
                                        'price' => $price,
                                        'priceCurrency' => 'AUD',
                                        'availability'=> $availability
                                    );
                                }else{
                                    $regularPrice = $product->get_regular_price();
                                    $Specification[] =  array(
                                        "@type" => "UnitPriceSpecification",
                                        "price" => $regularPrice,
                                        "priceCurrency" => "AUD",
                                        "priceType" => "https://schema.org/ListPrice"
                                    );
                                    $offers[] = array(
                                        '@type' => 'Offer',
                                        'price' => $price,
                                        'priceCurrency' => 'AUD',
                                        'availability'=> $availability,
                                        "priceSpecification" => $Specification
                                    );
                                }
                                
                                return $offers;
                        }
                    }
                }
                break;
            case 'product-category':
                $subCategory = "";
                if ( function_exists( 'wc_get_product' )){
                    $product = wc_get_product( $post->ID );
                    $subCategory = get_product_subcats_by_parent_slug( $product, $property);
                    if($subCategory){
                        return $subCategory;
                    }
                }
                break;
            case 'product-attribute':
                if( function_exists( 'wc_get_product' ) && $post ) {
                    $product = wc_get_product( $post->ID );
                    if ( $product ){
                        $attributes = array();
                        $attr_list = explode(",", $property);
                        foreach($attr_list as $attr_item){
                            $attr_item_value = $product->get_attribute( 'pa_' . $attr_item );
                            if($attr_item_value){
                                $attributes[] = array(
                                    '@type' => 'PropertyValue',
                                    'name' => $attr_item,
                                    'value' => $attr_item_value
                                );
                            }
                        }
                        return $attributes;
                    }
                }
                break;
            case 'single-product-attribute':
                if( function_exists( 'wc_get_product' ) && $post ) {
                    $product = wc_get_product( $post->ID );
                    if($product){
                        $attr_item_value = $product->get_attribute( 'pa_' . $property );
                        if($attr_item_value){
                            return $attr_item_value;
                        }   
                    }
                }
            case 'yoast':
                if( $post ){
                    switch ( $property ) {
                        case 'get_breadcrumb_items()':
                            if( function_exists( 'YoastSEO' ) ){
                                return self::get_yoast_breadcrumb_data( $post->ID );
                            }
                        case 'get_post_description':
                            if( function_exists( 'YoastSEO' ) ){
                                $meta = YoastSEO()->meta->for_post( $post_id );
                                $seoTescription = $meta->description;
                                return $seoTescription;
                            }
                        case 'get_post_title':
                            if( function_exists( 'YoastSEO' ) ){
                                $meta = YoastSEO()->meta->for_post( $post_id );
                                $seoTitle = $meta->title;
                                return $seoTitle;
                            }
                    }
                }
                break;
            case 'search_action_array':
                $search_action = array();
                $site_url = get_site_url() . '/search?q={search_term_string}';
                $search_action[] = array(
                    '@type' => 'SearchAction',
                    'target' => $site_url,
                    'query-input' => 'required name=search_term_string'
                );
                return $search_action;
                break;
            case 'author-profile':
                $authorprofile = array();
                $authorprofile[] = array(
                    '@type' => 'Person',
                    'name' => $property
                );
                return $authorprofile;
                break;
        }

        return null;
    }

    private static function get_yoast_breadcrumb_data( $post_id ) {
        $breadcrumbs_array = [];

        // Check if the main YoastSEO() function exists
        if ( ! function_exists( 'YoastSEO' ) ) {
            return $breadcrumbs_array;
        }

        $breadcrumbs = YoastSEO()->meta->for_post( $post_id )->breadcrumbs;

        // Loop through the results and format them
        if ( ! empty( $breadcrumbs ) ) {
            $index = 1;
            foreach ( $breadcrumbs as $breadcrumb ) {
                // Check if the 'url' and 'text' keys exist
                if ( isset( $breadcrumb['url'] ) && isset( $breadcrumb['text'] ) ) {
                    $breadcrumbs_array[] = [
                        '@type' => 'ListItem',
                        'position' => $index,
                        'name' => $breadcrumb['text'],
                        'item'  => $breadcrumb['url']
                    ];
                    $index++;
                }
            }
        }

        return $breadcrumbs_array;
    }

    private static function get_yoast_meta_description( $post_id ){
        $meta_key = '_yoast_wpseo_metadesc';
        $meta_description = get_post_meta( $post_id, $meta_key, true );
        return trim( $meta_description );
    }
}
