jQuery(document).ready(function($) {
    // Handle page type change
    $('#schema_page_type').on('change', function() {
        var selectedType = $(this).val();
        if (selectedType === 'custom_post' || selectedType === 'custom_post_archive') {
            $('#custom_post_slug_row').show();
        } else {
            $('#custom_post_slug_row').hide();
        }
    });

    // Schema properties definitions
    var schemaProperties = {
        webpage: [
            { key: 'name', label: 'Name', required: true },
            { key: 'url', label: 'URL', required: true },
            { key: 'description', label: 'Description', required: false },
            { key: 'inLanguage', label: 'Language', required: false },
            { key: 'datePublished', label: 'Date Published', required: false },
            { key: 'dateModified', label: 'Date Modified', required: false }
        ],
        article: [
            { key: 'headline', label: 'Headline', required: true },
            { key: 'author', label: 'Author', required: true },
            { key: 'publisher', label: 'Publisher', required: true },
            { key: 'datePublished', label: 'Date Published', required: true },
            { key: 'dateModified', label: 'Date Modified', required: false },
            { key: 'articleSection', label: 'Article Section', required: false },
            { key: 'image', label: 'Image URL', required: false }
        ],
        breadcrumb: [
            { key: 'itemListElement', label: 'Breadcrumb Items (JSON)', required: true }
        ],
        faq: [
            { key: 'mainEntity', label: 'FAQ Items (JSON)', required: true }
        ],
        local_business: [
            { key: 'name', label: 'Name', required: true },
            { key: 'address', label: 'Address', required: true },
            { key: 'telephone', label: 'Telephone', required: false },
            { key: 'url', label: 'URL', required: false },
            { key: 'priceRange', label: 'Price Range', required: false },
            { key: 'image', label: 'Image URL', required: false },
            { key: 'description', label: 'Description', required: false }
        ],
        image_metadata: [
            { key: 'contentUrl', label: 'Content URL', required: true },
            { key: 'url', label: 'URL', required: true },
            { key: 'width', label: 'Width', required: false },
            { key: 'height', label: 'Height', required: false },
            { key: 'caption', label: 'Caption', required: false }
        ],
        product: [
            { key: 'name', label: 'Name', required: true },
            { key: 'sku', label: 'SKU', required: true },
            { key: 'description', label: 'Description', required: false },
            { key: 'image', label: 'Image URL', required: false },
            { key: 'brand', label: 'Brand', required: false },
            { key: 'offers', label: 'Offers (JSON)', required: false },
            { key: 'aggregateRating', label: 'Aggregate Rating (JSON)', required: false },
            { key: 'additionalProperty', label: 'AdditionalProperty', required: false }
        ],
        review_snippet: [
            { key: 'itemReviewed', label: 'Item Reviewed', required: true },
            { key: 'author', label: 'Author', required: true },
            { key: 'reviewRating', label: 'Review Rating', required: true },
            { key: 'reviewBody', label: 'Review Body', required: false },
            { key: 'datePublished', label: 'Date Published', required: false }
        ],
        video: [
            { key: 'name', label: 'Name', required: true },
            { key: 'description', label: 'Description', required: true },
            { key: 'thumbnailUrl', label: 'Thumbnail URL', required: true },
            { key: 'uploadDate', label: 'Upload Date', required: true },
            { key: 'duration', label: 'Duration', required: false },
            { key: 'contentUrl', label: 'Content URL', required: false }
        ],
        organization: [
            { key: 'name', label: 'Name', required: true },
            { key: 'url', label: 'URL', required: false },
            { key: 'logo', label: 'Logo URL', required: false },
            { key: 'description', label: 'Description', required: false },
            { key: 'address', label: 'Address', required: false },
            { key: 'telephone', label: 'Telephone', required: false },
            { key: 'email', label: 'Email', required: false },
            { key: 'sameAs', label: 'Social Media URLs (JSON)', required: false }
        ],
        potential_action: [
            { key: 'target', label: 'Target', required: false },
            { key: 'name', label: 'Name', required: false },
        ],
        main_entity_of_page: [
            { key: '@id', label: 'ID', required: false }
        ],
        item_page: [
            { key: 'dateModified', label: 'Date Modified', required: false },
            { key: 'datePublished', label: 'Date Published', required: false },
            { key: 'inLanguage', label: 'Language', required: false },
            { key: 'name', label: 'Name', required: false },
            { key: 'url', label: 'URL', required: false },
            { key: 'isPartOf', label: 'Is Part Of', required: false },
        ],
        offer_schema: [
            { key: 'url', label: 'URL', required: false },
            { key: 'priceCurrency', label: 'Price Currency', required: false },
            { key: 'price', label: 'Price', required: false },
            { key: 'availability', label: 'Availability', required: false },
            { key: 'itemCondition', label: 'Item Condition', required: false },
            { key: 'seller', label: 'Seller', required: false },
        ],
        search_action: [
            { key: 'name', label: 'Name', required: false },
            { key: 'url', label: 'URL', required: false },
            { key: 'action_array', label: 'Action Array', required: false },
        ],
        profile: [
            { key: 'name', label: 'Name', required: false },
            { key: 'hasPart', label: 'Has Part', required: false },
        ],
        blog_posting: [
            { key: 'mainEntityOfPage', label: 'mainEntityOfPage', required: false },
            { key: 'headline', label: 'Headline', required: false },
            { key: 'description', label: 'Description', required: false },
            { key: 'image', label: 'Image', required: false },
            { key: 'author', label: 'Author', required: false },
            { key: 'datePublished', label: 'Date Published', required: false },
            { key: 'dateModified', label: 'Date Modified', required: false },
        ]
    };

    // Handle schema type change
    $(document).on('change', '.schema-type-select', function() {
        var $select = $(this);
        var type = $select.val();
        var $container = $select.closest('.schema-markup-item').find('.schema-properties-container');
        var $propertiesRow = $select.closest('.schema-markup-item').find('.schema-properties');
        var index = $select.closest('.schema-markup-item').data('index');

        if (type && schemaProperties[type]) {
            var propertiesHtml = '';
            schemaProperties[type].forEach(function(prop) {
                var requiredClass = prop.required ? 'required' : '';
                propertiesHtml += '<p><label>' + prop.label + (prop.required ? ' *' : '') + ':</label>' +
                    '<input type="text" name="schema_markups[' + index + '][properties][' + prop.key + ']" class="regular-text ' + requiredClass + '" /></p><br>';
            });
            $container.html(propertiesHtml);
            $propertiesRow.show();
        } else {
            $container.html('');
            $propertiesRow.hide();
        }
    });

    // Add new markup
    var markupIndex = $('#schema-markups .schema-markup-item').length;
    $('#add-markup').on('click', function() {
        var markupHtml = '<div class="schema-markup-item" data-index="' + markupIndex + '">' +
            '<h4>Schema Markup ' + (markupIndex + 1) + ' <button type="button" class="button remove-markup">Remove</button></h4>' +
            '<table class="form-table">' +
                '<tr>' +
                    '<th scope="row">Schema Type</th>' +
                    '<td>' +
                        '<select name="schema_markups[' + markupIndex + '][type]" class="schema-type-select">' +
                            '<option value="">Select Type</option>' +
                            '<option value="webpage">Webpage</option>' +
                            '<option value="article">Article</option>' +
                            '<option value="breadcrumb">Breadcrumb</option>' +
                            '<option value="faq">FAQ</option>' +
                            '<option value="local_business">Local Business</option>' +
                            '<option value="image_metadata">Image Metadata</option>' +
                            '<option value="product">Product</option>' +
                            '<option value="review_snippet">Review Snippet</option>' +
                            '<option value="video">Video</option>' +
                            '<option value="organization">Organization</option>' +
                            '<option value="potential_action">Potential Action</option>' +
                            '<option value="main_entity_of_page">Main Entity of Page</option>' +
                            '<option value="item_page">ItemPage</option>' +
                            '<option value="offer_schema">Offer schema</option>' +
                            '<option value="search_action">Search Action</option>' +
                            '<option value="profile">ProfilePage</option>' +
                            '<option value="blog_posting">BlogPosting</option>' +
                        '</select>' +
                    '</td>' +
                '</tr>' +
                '<tr class="schema-properties" style="display: none;">' +
                    '<th scope="row">Properties</th>' +
                    '<td>' +
                        '<div class="schema-properties-container"></div>' +
                    '</td>' +
                '</tr>' +
            '</table>' +
        '</div>';
        $('#schema-markups').append(markupHtml);
        markupIndex++;
    });

    // Remove markup
    $(document).on('click', '.remove-markup', function() {
        $(this).closest('.schema-markup-item').remove();
    });

    // Trigger change on load for existing markups that don't have properties rendered
    $('.schema-type-select').each(function() {
        var $select = $(this);
        var $container = $select.closest('.schema-markup-item').find('.schema-properties-container');
        // Only trigger change if container is empty (no server-side rendered properties)
        if ($container.is(':empty')) {
            $select.trigger('change');
        }
    });
});
