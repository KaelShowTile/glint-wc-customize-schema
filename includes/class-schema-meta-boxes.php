<?php
/**
 * Schema Meta Boxes Class
 * Handles meta boxes and fields for schema settings
 */

class Schema_Meta_Boxes {

    //Initialize the class
    public static function init() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ) );
    }

    //Add meta boxes
    public static function add_meta_boxes() {
        add_meta_box(
            'schema_settings',
            __( 'Schema Settings', 'glint-schema' ),
            array( __CLASS__, 'render_meta_box' ),
            'schema_setting',
            'normal',
            'high'
        );
    }

    //Render meta box
    public static function render_meta_box( $post ) {
        wp_nonce_field( 'schema_meta_box', 'schema_meta_box_nonce' );

        $page_type = get_post_meta( $post->ID, '_schema_page_type', true );
        $custom_post_slug = get_post_meta( $post->ID, '_schema_custom_post_slug', true );
        $schema_markups = get_post_meta( $post->ID, '_schema_markups', true );
        if ( ! is_array( $schema_markups ) ) {
            $schema_markups = array();
        }

        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'Page/Post Type', 'glint-schema' ); ?></th>
                <td>
                    <select name="schema_page_type" id="schema_page_type">
                        <option value=""><?php _e( 'Select Type', 'glint-schema' ); ?></option>
                        <option value="blog_post" <?php selected( $page_type, 'blog_post' ); ?>><?php _e( 'Blog Post', 'glint-schema' ); ?></option>
                        <option value="blog_archive" <?php selected( $page_type, 'blog_archive' ); ?>><?php _e( 'Blog Archive Page', 'glint-schema' ); ?></option>
                        <option value="product_post" <?php selected( $page_type, 'product_post' ); ?>><?php _e( 'Product Post', 'glint-schema' ); ?></option>
                        <option value="product_archive" <?php selected( $page_type, 'product_archive' ); ?>><?php _e( 'Product Archive Page', 'glint-schema' ); ?></option>
                        <option value="custom_post" <?php selected( $page_type, 'custom_post' ); ?>><?php _e( 'Custom Post', 'glint-schema' ); ?></option>
                        <option value="custom_post_archive" <?php selected( $page_type, 'custom_post_archive' ); ?>><?php _e( 'Custom Post Archive', 'glint-schema' ); ?></option>
                        <option value="page" <?php selected( $page_type, 'page' ); ?>><?php _e( 'Page', 'glint-schema' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr id="custom_post_slug_row" style="display: <?php echo in_array( $page_type, array( 'custom_post', 'custom_post_archive' ) ) ? 'table-row' : 'none'; ?>;">
                <th scope="row"><?php _e( 'Custom Post Type Slug', 'glint-schema' ); ?></th>
                <td>
                    <input type="text" name="schema_custom_post_slug" value="<?php echo esc_attr( $custom_post_slug ); ?>" class="regular-text" />
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Schema Markups', 'glint-schema' ); ?></h3>
        <div id="schema-markups">
            <?php foreach ( $schema_markups as $index => $markup ) : ?>
                <div class="schema-markup-item" data-index="<?php echo $index; ?>">
                    <h4><?php _e( 'Schema Markup', 'glint-schema' ); ?> <?php echo $index + 1; ?> <button type="button" class="button remove-markup"><?php _e( 'Remove', 'glint-schema' ); ?></button></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e( 'Schema Type', 'glint-schema' ); ?></th>
                            <td>
                                <select name="schema_markups[<?php echo $index; ?>][type]" class="schema-type-select">
                                    <option value=""><?php _e( 'Select Type', 'glint-schema' ); ?></option>
                                    <option value="webpage" <?php selected( $markup['type'], 'webpage' ); ?>><?php _e( 'Webpage', 'glint-schema' ); ?></option>
                                    <option value="article" <?php selected( $markup['type'], 'article' ); ?>><?php _e( 'Article', 'glint-schema' ); ?></option>
                                    <option value="breadcrumb" <?php selected( $markup['type'], 'breadcrumb' ); ?>><?php _e( 'Breadcrumb', 'glint-schema' ); ?></option>
                                    <option value="faq" <?php selected( $markup['type'], 'faq' ); ?>><?php _e( 'FAQ', 'glint-schema' ); ?></option>
                                    <option value="local_business" <?php selected( $markup['type'], 'local_business' ); ?>><?php _e( 'Local Business', 'glint-schema' ); ?></option>
                                    <option value="image_metadata" <?php selected( $markup['type'], 'image_metadata' ); ?>><?php _e( 'Image Metadata', 'glint-schema' ); ?></option>
                                    <option value="product" <?php selected( $markup['type'], 'product' ); ?>><?php _e( 'Product', 'glint-schema' ); ?></option>
                                    <option value="review_snippet" <?php selected( $markup['type'], 'review_snippet' ); ?>><?php _e( 'Review Snippet', 'glint-schema' ); ?></option>
                                    <option value="video" <?php selected( $markup['type'], 'video' ); ?>><?php _e( 'Video', 'glint-schema' ); ?></option>
                                    <option value="organization" <?php selected( $markup['type'], 'organization' ); ?>><?php _e( 'Organization', 'glint-schema' ); ?></option>
                                    <option value="potential_action" <?php selected( $markup['type'], 'potential_action' ); ?>><?php _e( 'Potential Action', 'glint-schema' ); ?></option>
                                    <option value="main_entity_of_page" <?php selected( $markup['type'], 'main_entity_of_page' ); ?>><?php _e( 'Main Entity of Page', 'glint-schema'); ?></option>
                                    <option value="item_page" <?php selected( $markup['type'], 'item_page' ); ?>><?php _e( 'Item Page' , 'glint-schema' ); ?></option>
                                    <option value="offer_schema" <?php selected( $markup['type'], 'offer_schema' ); ?>><?php _e( 'Offer' , 'glint-schema' ); ?></option>
                                    <option value="search_action" <?php selected( $markup['type'], 'search_action' ); ?>><?php _e( 'Search Action' , 'glint-schema' ); ?></option>
                                    <option value="profile" <?php selected( $markup['type'], 'profile' ); ?>><?php _e( 'ProfilePage' , 'glint-schema' ); ?></option>
                                    <option value="blog_posting" <?php selected( $markup['type'], 'blog_posting' ); ?>><?php _e( 'BlogPosting' , 'glint-schema' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="schema-properties" style="display: <?php echo !empty($markup['properties']) ? 'table-row' : 'none'; ?>;">
                            <th scope="row"><?php _e( 'Properties', 'glint-schema' ); ?></th>
                            <td>
                                <div class="schema-properties-container">
                                    <?php if (!empty($markup['properties'])) : ?>
                                        <?php
                                        $properties = self::get_schema_properties($markup['type']);
                                        foreach ($properties as $prop) :
                                            $value = isset($markup['properties'][$prop['key']]) ? $markup['properties'][$prop['key']] : '';
                                            $required_class = $prop['required'] ? 'required' : '';
                                        ?>
                                            <p><label><?php echo esc_html($prop['label']); ?><?php echo $prop['required'] ? ' *' : ''; ?>:</label>
                                            <input type="text" name="schema_markups[<?php echo $index; ?>][properties][<?php echo esc_attr($prop['key']); ?>]" value="<?php echo esc_attr($value); ?>" class="regular-text <?php echo $required_class; ?>" /></p><br>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button" id="add-markup"><?php _e( 'Add Schema Markup', 'glint-schema' ); ?></button>
        <?php
    }

    //Save meta boxes
    public static function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['schema_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['schema_meta_box_nonce'], 'schema_meta_box' ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'schema_setting' !== $_POST['post_type'] ) {
            return;
        }

        update_post_meta( $post_id, '_schema_page_type', sanitize_text_field( $_POST['schema_page_type'] ) );
        update_post_meta( $post_id, '_schema_custom_post_slug', sanitize_text_field( $_POST['schema_custom_post_slug'] ) );

        if ( isset( $_POST['schema_markups'] ) && is_array( $_POST['schema_markups'] ) ) {
            $schema_markups = array();
            foreach ( $_POST['schema_markups'] as $markup ) {
                if ( ! empty( $markup['type'] ) ) {
                    $schema_markups[] = array(
                        'type' => sanitize_text_field( $markup['type'] ),
                        'properties' => isset( $markup['properties'] ) ? self::sanitize_properties( $markup['properties'] ) : array(),
                    );
                }
            }
            update_post_meta( $post_id, '_schema_markups', $schema_markups );
        } else {
            delete_post_meta( $post_id, '_schema_markups' );
        }
    }

    //Get schema properties for a type
    private static function get_schema_properties( $type ) {
        $properties = array(
            'webpage' => array(
                array( 'key' => 'name', 'label' => 'Name', 'required' => true ),
                array( 'key' => 'url', 'label' => 'URL', 'required' => true ),
                array( 'key' => 'description', 'label' => 'Description', 'required' => false ),
                array( 'key' => 'inLanguage', 'label' => 'Language', 'required' => false ),
                array( 'key' => 'datePublished', 'label' => 'Date Published', 'required' => false ),
                array( 'key' => 'dateModified', 'label' => 'Date Modified', 'required' => false )
            ),
            'article' => array(
                array( 'key' => 'headline', 'label' => 'Headline', 'required' => true ),
                array( 'key' => 'author', 'label' => 'Author', 'required' => true ),
                array( 'key' => 'publisher', 'label' => 'Publisher', 'required' => true ),
                array( 'key' => 'datePublished', 'label' => 'Date Published', 'required' => true ),
                array( 'key' => 'dateModified', 'label' => 'Date Modified', 'required' => false ),
                array( 'key' => 'articleSection', 'label' => 'Article Section', 'required' => false ),
                array( 'key' => 'image', 'label' => 'Image URL', 'required' => false )
            ),
            'breadcrumb' => array(
                array( 'key' => 'itemListElement', 'label' => 'Breadcrumb Items (JSON)', 'required' => true )
            ),
            'faq' => array(
                array( 'key' => 'mainEntity', 'label' => 'FAQ Items (JSON)', 'required' => true )
            ),
            'local_business' => array(
                array( 'key' => 'name', 'label' => 'Name', 'required' => true ),
                array( 'key' => 'address', 'label' => 'Address', 'required' => true ),
                array( 'key' => 'telephone', 'label' => 'Telephone', 'required' => false ),
                array( 'key' => 'url', 'label' => 'URL', 'required' => false ),
                array( 'key' => 'priceRange', 'label' => 'Price Range', 'required' => false ),
                array( 'key' => 'image', 'label' => 'Image URL', 'required' => false ),
                array( 'key' => 'description', 'label' => 'Description', 'required' => false )
            ),
            'image_metadata' => array(
                array( 'key' => 'contentUrl', 'label' => 'Content URL', 'required' => true ),
                array( 'key' => 'url', 'label' => 'URL', 'required' => true ),
                array( 'key' => 'width', 'label' => 'Width', 'required' => false ),
                array( 'key' => 'height', 'label' => 'Height', 'required' => false ),
                array( 'key' => 'caption', 'label' => 'Caption', 'required' => false )
            ),
            'product' => array(
                array( 'key' => 'id', 'label' => 'ID', 'required' => true ),
                array( 'key' => 'name', 'label' => 'Name', 'required' => true ),
                array( 'key' => 'sku', 'label' => 'SKU', 'required' => false ),
                array( 'key' => 'category', 'label' => 'Category', 'required' => false ),
                array( 'key' => 'description', 'label' => 'Description', 'required' => false ),
                array( 'key' => 'image', 'label' => 'Image URL', 'required' => false ),
                array( 'key' => 'brand', 'label' => 'Brand', 'required' => false ),
                array( 'key' => 'design', 'label' => 'Design', 'required' => false ),
                array( 'key' => 'material', 'label' => 'Material', 'required' => false ),
                array( 'key' => 'colour', 'label' => 'Colour', 'required' => false ),
                array( 'key' => 'size', 'label' => 'Size', 'required' => false ),
                array( 'key' => 'offers', 'label' => 'Offers (JSON)', 'required' => false ),
                array( 'key' => 'aggregateRating', 'label' => 'Aggregate Rating (JSON)', 'required' => false ),
                array( 'key' => 'additionalProperty', 'label' => 'AdditionalProperty', 'required' => false )
            ),
            'review_snippet' => array(
                array( 'key' => 'itemReviewed', 'label' => 'Item Reviewed', 'required' => true ),
                array( 'key' => 'author', 'label' => 'Author', 'required' => true ),
                array( 'key' => 'reviewRating', 'label' => 'Review Rating', 'required' => true ),
                array( 'key' => 'reviewBody', 'label' => 'Review Body', 'required' => false ),
                array( 'key' => 'datePublished', 'label' => 'Date Published', 'required' => false )
            ),
            'video' => array(
                array( 'key' => 'name', 'label' => 'Name', 'required' => true ),
                array( 'key' => 'description', 'label' => 'Description', 'required' => true ),
                array( 'key' => 'thumbnailUrl', 'label' => 'Thumbnail URL', 'required' => true ),
                array( 'key' => 'uploadDate', 'label' => 'Upload Date', 'required' => true ),
                array( 'key' => 'duration', 'label' => 'Duration', 'required' => false ),
                array( 'key' => 'contentUrl', 'label' => 'Content URL', 'required' => false )
            ),
            'organization' => array(
                array( 'key' => 'name', 'label' => 'Name', 'required' => true ),
                array( 'key' => 'url', 'label' => 'URL', 'required' => false ),
                array( 'key' => 'logo', 'label' => 'Logo URL', 'required' => false ),
                array( 'key' => 'description', 'label' => 'Description', 'required' => false ),
                array( 'key' => 'address', 'label' => 'Address', 'required' => false ),
                array( 'key' => 'telephone', 'label' => 'Telephone', 'required' => false ),
                array( 'key' => 'email', 'label' => 'Email', 'required' => false ),
                array( 'key' => 'sameAs', 'label' => 'Social Media URLs (JSON)', 'required' => false )
            ),
            'potential_action' => array(
                array( 'key' => 'target', 'label' => 'Target', 'required' => true ),
                array( 'key' => 'name', 'label' => 'Name', 'required' => true )
            ),
            'main_entity_of_page' => array(
                array( 'key' => '@id', 'label' => 'ID', 'required' => true )
            ),
            'item_page' => array(
                array( 'key' => 'dateModified', 'label' => 'Date Modified', 'required' => false ),
                array( 'key' => 'datePublished', 'label' => 'Date Published', 'required' => false ),
                array( 'key' => 'inLanguage', 'label' => 'Language', 'required' => false ),
                array( 'key' => 'name', 'label' => 'Name', 'required' => false ),
                array( 'key' => 'url', 'label' => 'URL', 'required' => false ),
                array( 'key' => 'isPartOf', 'label' => 'Is Part Of', 'required' => false )
            ),
            'offer_schema' => array(
                array( 'key' => 'url', 'label' => 'URL', 'required' => false ),
                array( 'key' => 'priceCurrency', 'label' => 'Price Currency', 'required' => false ),
                array( 'key' => 'price', 'label' => 'Price', 'required' => false ),
                array( 'key' => 'availability', 'label' => 'Availability', 'required' => false ),
                array( 'key' => 'itemCondition', 'label' => 'Item Condition', 'required' => false ),
                array( 'key' => 'seller', 'label' => 'Seller', 'required' => false ),
            ),

            'search_action' => array(
                array( 'key' => 'name', 'label' => 'Name', 'required' => false ),
                array( 'key' => 'url', 'label' => 'URL', 'required' => false ),
                array( 'key' => 'action_array', 'label' => 'Action Array', 'required' => false ),
            ),

            'profile' => array(
                array( 'key' => 'name', 'label' => 'Name', 'required' => false ),
                array( 'key' => 'hasPart', 'label' => 'Has Part', 'required' => false ),
            ),

            'blog_posting' => array(
                array( 'key' => 'mainEntityOfPage', 'label' => 'mainEntityOfPage', 'required' => false ),
                array( 'key' => 'headline', 'label' => 'Headline', 'required' => false ),
                array( 'key' => 'description', 'label' => 'Description', 'required' => false ),
                array( 'key' => 'image', 'label' => 'Image', 'required' => false ),
                array( 'key' => 'author', 'label' => 'Author', 'required' => false ),
                array( 'key' => 'datePublished', 'label' => 'Date Published', 'required' => false ),
                array( 'key' => 'dateModified', 'label' => 'Date Modified', 'required' => false ),
            ),
        );

        return isset( $properties[$type] ) ? $properties[$type] : array();
    }

    //Sanitize properties array
    private static function sanitize_properties( $properties ) {
        $sanitized = array();
        if ( is_array( $properties ) ) {
            foreach ( $properties as $key => $value ) {
                $sanitized[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
            }
        }
        return $sanitized;
    }
}
