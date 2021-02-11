<?php

/**
 * Class based on https://github.com/Dudo1985/phpFieldsHelper
 *
 * Used as helper to create these html element
 *
 * <input type="text">
 * <select>
 * <textarea>
 * <input type="radio">
 *
 * Class YasrPhpFieldsHelper
 */

if (!class_exists('YasrPhpFieldsHelper') ) {

    class YasrPhpFieldsHelper {

        /**
         * Default class
         *
         * @var string
         */
        public static $field_class;

        public function __construct($field_class = false) {
            if ($field_class) {
                self::$field_class = htmlspecialchars($field_class);
            }
        }

        /**
         * @param bool|string     $title
         * @param bool|string     $class
         * @param array           $options
         * @param bool|string     $name
         * @param bool|string|int $default_value
         * @param bool|string     $id
         *
         * @return string
         */

        public static function radio(
            $title = false, $class = false, $options = [], $name = false, $default_value = false, $id = false
        ) {

            $attribute     = self::escape_attributes($class, $title, $name, $id, $default_value, false);
            $radio_options = self::escape_array($options);

            $container     = '';
            $end_container = '';
            $title_string  = '';

            if ($attribute['title']) {
                $title_string .= '<strong>' . $attribute['title'] . '</strong><br />';
            }

            if (is_array($radio_options)) {

                $container .= '<div class="yasr-indented-answer">';

                $radio_fields = '';
                foreach ($radio_options as $value => $label) {
                    $id_string = $attribute['id'] . '-' . strtolower(trim(str_replace(' ', '', $value)));
                    //must be inside foreach, or when loop arrive to last element
                    //checked is defined
                    $checked = '';

                    //escape_attributes use htmlspecialchars that always return a string, so control must be weak
                    /** @noinspection TypeUnsafeComparisonInspection */
                    if ($attribute['value'] == $value) {
                        $checked = 'checked';
                    }

                    //string value must be empty
                    if ($value === 0) {
                        $value = '';
                    }

                    $radio_fields .= sprintf(
                        '<div>
                        <label for="%s">
                            <input type="radio"
                                name="%s"
                                value="%s"
                                class="%s"
                                id="%s"
                                %s
                            >
                            %s
                        </label>
                    </div>', $id_string, $attribute['name'], $value, $attribute['class'], $id_string, $checked,
                        __(ucfirst($label), 'yet-another-stars-rating')
                    );

                } //end foreach

                $end_container .= '</div>';

                return $container . $title_string . $radio_fields . $end_container;
            }
            return false;
        }

        /**
         * @param bool|string     $class
         * @param bool|string|int $label
         * @param bool|string|int $name
         * @param bool|string|int $id
         * @param bool|string|int $placeholder
         * @param bool|string|int $default_value
         *
         * @return string
         */
        public static function text(
            $class = false, $label = false, $name = false, $id = false, $placeholder = false, $default_value = false
        ) {
            $attribute = self::escape_attributes($class, $label, $name, $id, $default_value, $placeholder);

            $container     = "<div class='$attribute[class]'>";
            $label_string  = "<label for='$attribute[id]'>$attribute[label]</label>";
            $input_text    = "<input type='text' name='$attribute[name]' id='$attribute[id]' value='$attribute[value]'
                            placeholder='$attribute[placeholder]'>";
            $end_container = "</div>";

            return ($container . $label_string . $input_text . $end_container);
        }

        /**
         * @param bool|string     $class
         * @param bool|string|int $label
         * @param array           $options
         * @param bool|string|int $name
         * @param bool|string|int $id
         * @param bool|string|int $default_value
         *
         * @return string
         */
        public static function select(
            $class = false, $label = false, $options = [], $name = false, $id = false, $default_value = false
        ) {
            $attribute      = self::escape_attributes($class, $label, $name, $id, $default_value);
            $select_options = self::escape_array($options);

            $container     = "<div class='$attribute[class]'>";
            $label_string  = "<label for='$attribute[id]'>$attribute[label]</label>";
            $select        = "<select name='$attribute[name]' id='$attribute[id]'>";
            $end_select    = "</select>";
            $end_container = "</div>";

            foreach ($select_options as $key => $option) {
                if ($option === $attribute['value']) {
                    $select .= "<option value='$option' selected>$option</option>";
                }
                else {
                    $select .= "<option value='$option'>$option</option>";
                }
            }

            return ($container . $label_string . $select . $end_select . $end_container);
        }

        /**
         * @param bool|string     $class
         * @param bool|string|int $label
         * @param bool|string|int $name
         * @param bool|string|int $id
         * @param bool|string|int $placeholder
         * @param bool|string|int $default_value
         *
         * @return string
         */
        public static function textArea(
            $class = false, $label = false, $name = false, $id = false, $placeholder = false, $default_value = false
        ) {
            $attribute = self::escape_attributes($class, $label, $name, $id, $default_value, $placeholder);

            $container     = "<div class='$attribute[class]'>";
            $label_string  = "<label for='$attribute[id]'>$attribute[label]</label>";
            $textarea
                           = "<textarea name='$attribute[name]' id='$attribute[id]' placeholder='$attribute[placeholder]'>";
            $end_textarea  = "</textarea>";
            $end_container = "</div>";

            return ($container . $label_string . $textarea . $attribute['value'] . $end_textarea . $end_container);
        }


        /**
         * @param bool|string     $class
         * @param bool|string|int $label
         * @param bool|string|int $name
         * @param bool|string|int $id
         * @param bool|string|int $placeholder
         * @param bool|string|int $default_value
         *
         * @return array
         */
        private static function escape_attributes(
            $class = false, $label = false, $name = false, $id = false, $default_value = false, $placeholder = false
        ) {

            $dbt    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $caller = isset($dbt[1]['function']) ? $dbt[1]['function'] : null;

            //defalt value
            $title_or_label = 'label';

            if ($caller === 'radio') {
                $title_or_label = 'title';
                if (!$name) {
                    $name = 'radio_group';
                }
            }

            //Use the self::field_class attribute if $class is false or empty
            if (!$class && self::$field_class) {
                $class = self::$field_class;
            }

            //if id is not set but name is, id get same value as name
            if (!$id && $name) {
                $id = $name;
            }
            //viceversa
            elseif (!$name && $id) {
                $name = $id;
            }

            //Use a random string (uniqueid and str_shuffle to add randomness) if id is still empty
            if (!$id) {
                $id = str_shuffle(uniqid('', true));
            }

            return array(
                'class'         => htmlspecialchars($class, ENT_QUOTES),
                'id'            => htmlspecialchars($id, ENT_QUOTES),
                $title_or_label => htmlspecialchars($label, ENT_QUOTES),
                'name'          => htmlspecialchars($name, ENT_QUOTES),
                'placeholder'   => htmlspecialchars($placeholder, ENT_QUOTES),
                'value'         => htmlspecialchars($default_value, ENT_QUOTES),
            );
        }

        private static function escape_array($array = []) {
            $cleaned_array = [];
            if (!is_array($array)) {
                return $cleaned_array;
            }

            foreach ($array as $key => $value) {
                $key                 = htmlspecialchars($key, ENT_QUOTES);
                $cleaned_array[$key] = htmlspecialchars($value, ENT_QUOTES);
            }

            return $cleaned_array;
        }

        /**
         * @param $name
         * @param $class
         * @param $db_value
         * @param $id
         * return void
         *
         * @since 2.3.3
         */
        public static function radioSelectSize($name, $class, $db_value = false, $id = false) {
            $array_size = array('small', 'medium', 'large');

            foreach ($array_size as $size) {
                $id_string = $id . $size;

                //must be inside for each, or when loop arrive to last element
                //checked is defined
                $checked = '';

                //If db_value === false, there is no need to check for db value
                //so checked is the medium star (i.e. ranking page)
                if ($db_value === false) {
                    if ($size === 'medium') {
                        $checked = 'checked';
                    }
                }
                else if ($db_value === $size) {
                    $checked = 'checked';
                }

                echo sprintf(
                    '<div class="yasr-option-div">
                      <label for="%s">
                          <input type="radio"
                              name="%s"
                              value="%s"
                              class="%s"
                              id="%s"
                              %s
                          >
                          <img src="%syasr-stars-%s.png"
                               class="yasr-img-option-size" alt=%s>
                          <span class="yasr-text-options-size">
                              %s
                          </span>
                      </label>
                  </div>', $id_string, $name, $size, $class, $id_string, $checked, YASR_IMG_DIR, $size, $size,
                    __(ucwords($size), 'yet-another-stars-rating')
                );

            } //end foreach
        }
    }

}
