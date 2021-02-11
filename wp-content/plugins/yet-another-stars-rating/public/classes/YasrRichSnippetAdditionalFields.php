<?php

/**
 * This class hook to yasr_filter_existing_schema, get the post meta
 * yasr_schema_additional_fields, and depending of the itemType selected
 * returns the schema info
 *
 * Class YasrRichSnippetAdditionalFields
 */
class YasrRichSnippetAdditionalFields {

    public function addFilters() {
        add_filter('yasr_filter_schema_title',    array($this, 'filter_title'));
        add_filter('yasr_filter_existing_schema', array($this, 'additional_schema'));
    }

    /**
     * Filter the title that will be used in the rich snippets
     * Use a user defined title if found, or the post title otherwise
     * @author Dario Curvino <@dudo>
     *
     * @param $post_id
     *
     * @return mixed|string
     */
    public function filter_title($post_id) {
        $saved_data = $this->saved_data();

        //if is not empty, overwrite the title with custom itemType name
        if(!empty($saved_data['yasr_schema_title'])) {
            $schema_title = $saved_data['yasr_schema_title'];
        } else {
            //Here I don't use get_the_title because it run after filters are applied.
            //This causes that stars near title will appear in schema title
            //https://wordpress.stackexchange.com/questions/257499/get-title-without-filterthe-title
            $schema_title = get_post_field('post_title', $post_id, 'raw');
        }
        return $schema_title;
    }

    public function additional_schema($rich_snippet) {
        $saved_data = $this->saved_data();

        //avoid undefined
        $more_rich_snippet = array();

        //get the select itemType
        $review_choosen = yasr_get_itemType();

        if($review_choosen === 'Product') {
            $more_rich_snippet = $this->itemProduct($saved_data);
        }
        if($review_choosen === 'LocalBusiness') {
            $more_rich_snippet = $this->localBusiness($saved_data);
        }
        if($review_choosen === 'Recipe') {
            $more_rich_snippet = $this->recipe($saved_data);
        }

        if($review_choosen === 'SoftwareApplication') {
            $more_rich_snippet = $this->softwareApplication($saved_data);
        }

        if($review_choosen === 'Book') {
            $more_rich_snippet = $this->book($saved_data);
        }

        if($review_choosen === 'Movie') {
            $more_rich_snippet = $this->movie($saved_data);
        }

        return array_merge($rich_snippet, $more_rich_snippet);

    }

    private function itemProduct($saved_data) {
        $global_identifer_name = $saved_data['yasr_product_global_identifier_select'];

        $rich_snippet['brand']                = $saved_data['yasr_product_brand'];
        $rich_snippet['sku']                  = $saved_data['yasr_product_sku'];
        $rich_snippet[$global_identifer_name] = $saved_data['yasr_product_global_identifier_value'];

        if(!empty($saved_data['yasr_product_price'])) {
            $rich_snippet['offers'] = array(
                '@type'           => 'Offer',
                'price'           => $saved_data['yasr_product_price'],
                'priceCurrency'   => $saved_data['yasr_product_price_currency'],
                'priceValidUntil' => $saved_data['yasr_product_price_valid_until'],
                'availability'    => $saved_data['yasr_product_price_availability'],
                'url'             => $saved_data['yasr_product_price_url'],
            );

        }
        return $rich_snippet;
    }

    private function localBusiness($saved_data) {
        $rich_snippet['address']            = $saved_data['yasr_localbusiness_address'];
        $rich_snippet['priceRange']         = $saved_data['yasr_localbusiness_pricerange'];
        $rich_snippet['telephone']          = $saved_data['yasr_localbusiness_telephone'];

        return $rich_snippet;
    }

    private function recipe($saved_data) {

        $instruction_array_clean = array();
        $ingredient_array        = array();

        if(!empty($saved_data['yasr_recipe_recipeinstructions'])) {
            $instruction_array = explode(PHP_EOL, $saved_data['yasr_recipe_recipeinstructions']);
            $i=0;
            $j=1;
            foreach ($instruction_array as $instrunction) {
                $instruction_array_clean[$i]['@type'] = "HowToStep";
                $instruction_array_clean[$i]["itemListElement"] = array(
                    '@type'    => 'ListItem',
                    'position' => $j,
                    'name'     => $instrunction
                );
                $i++;
                $j++;
            }
        }

        if(!empty($saved_data['yasr_recipe_recipeingredient'])) {
            $ingredient_array = explode(PHP_EOL, $saved_data['yasr_recipe_recipeingredient']);
        }

        if(!empty($saved_data['yasr_recipe_nutrition'])) {
            $rich_snippet['nutrition'] = array(
                "@type"    => "NutritionInformation",
                "calories" => $saved_data['yasr_recipe_nutrition'] . " calories",
            );

        }

        $rich_snippet['author'] = array(
            '@type' => 'Person',
            'name'  => get_the_author()
        );

        $rich_snippet['cookTime']           = $saved_data['yasr_recipe_cooktime'];
        $rich_snippet['description']        = $saved_data['yasr_recipe_description'];
        $rich_snippet['keywords']           = $saved_data['yasr_recipe_keywords'];
        $rich_snippet['prepTime']           = $saved_data['yasr_recipe_preptime'];
        $rich_snippet['recipeCategory']     = $saved_data['yasr_recipe_recipecategory'];
        $rich_snippet['recipeCuisine']      = $saved_data['yasr_recipe_recipecuisine'];
        $rich_snippet['recipeIngredient']   = $ingredient_array;
        $rich_snippet['recipeInstructions'] = $instruction_array_clean;
        $rich_snippet['video']              = $saved_data['yasr_recipe_video'];

        return $rich_snippet;
    }

    private function softwareApplication($saved_data) {

        $rich_snippet['applicationCategory'] = $saved_data['yasr_software_application'];
        $rich_snippet['operatingSystem']     = $saved_data['yasr_software_os'];

        if(!empty($saved_data['yasr_software_price'])) {
            $rich_snippet['offers'] = array(
                '@type'           => 'Offer',
                'price'           => $saved_data['yasr_software_price'],
                'priceCurrency'   => $saved_data['yasr_software_price_currency'],
                'priceValidUntil' => $saved_data['yasr_software_price_valid_until'],
                'availability'    => $saved_data['yasr_software_price_availability'],
                'url'             => $saved_data['yasr_software_price_url'],
            );

        }
        return $rich_snippet;
    }

    private function book($saved_data) {

        if(!empty($saved_data['yasr_book_author'])) {
            $rich_snippet['author'] = array(
                '@type'           => 'Person',
                'name'            => $saved_data['yasr_book_author'],
            );
        }

        $rich_snippet['bookEdition']    = $saved_data['yasr_book_bookedition'];
        $rich_snippet['bookFormat']     = $saved_data['yasr_book_bookformat'];
        $rich_snippet['isbn']           = $saved_data['yasr_book_isbn'];
        $rich_snippet['numberOfPages']  = $saved_data['yasr_book_number_of_pages'];

        return $rich_snippet;
    }

    private function movie($saved_data) {

        $actors_array_clean     = array();
        $director_array_clean   = array();

        if(!empty($saved_data['yasr_movie_actor'])) {
            $actors_array = explode(PHP_EOL, $saved_data['yasr_movie_actor']);
            $i=0;
            foreach ($actors_array as $actor) {
                $actors_array_clean[$i]['@type'] = "Person";
                $actors_array_clean[$i]['name'] = $actor;

                $i++;
            }
        }

        $rich_snippet['actor'] = $actors_array_clean;

        if(!empty($saved_data['yasr_movie_director'])) {
            $director_array = explode(PHP_EOL, $saved_data['yasr_movie_director']);
            $i=0;
            foreach ($director_array as $director) {
                $director_array_clean[$i]['@type'] = "Person";
                $director_array_clean[$i]['name'] = $director;

                $i++;
            }
        }

        $rich_snippet['director'] = $director_array_clean;

        $rich_snippet['duration']    = $saved_data['yasr_movie_duration'];
        $rich_snippet['dateCreated'] = $saved_data['yasr_movie_datecreated'];

        return $rich_snippet;
    }

    private function saved_data() {
        $saved_data = get_post_meta(get_the_ID(), 'yasr_schema_additional_fields', true);
        //avoid undefined
        if(!is_array($saved_data)) {
            $saved_data = array();
        }

        $array_item_type_info = json_decode(YASR_SUPPORTED_SCHEMA_TYPES_ADDITIONAL_FIELDS, true);

        foreach ($array_item_type_info as $item_type) {
            //avoid undefined
            if(!isset($saved_data[$item_type])) {
                $saved_data[$item_type] = '';
            }
        }

        return $saved_data;
    }
}