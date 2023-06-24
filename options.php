<?php

class OptionsPage
{
    //Holds the values
    private $options;

    public function __construct()
    {
        add_action('admin_menu', array( $this, 'add_plugin_page' ));
        add_action('admin_init', array( $this, 'page_init' ));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This creates a page under settings
        add_menu_page(
            'Upsell Pop-up settings', // page <title>Title</title>
            'Upsell Pop-up', // link text
            'manage_options', // user capabilities
            'upsell-popup-options', // page slug
            array( $this, 'upsell_content_callback' ), // this function prints the page content
            'dashicons-images-alt2', // icon (from Dashicons for example)
            4 // menu position
        );
    }

    public function upsell_content_callback()
    {
        // Set class property
        $this->options = get_option('my_option_name');
        ?>
<div class="wrap">
    <h1>Upsell Pop-up Settings</h1>
    <form method="post" action="options.php">
        <div id='menu'><a href='?page=upsell-popup-options&section=general'>General</a> | <a
                href='?page=upsell-popup-options&section=style'>Style</a> | <a
                href='?page=upsell-popup-options&section=content'>Content</a></div>
        <?php
        settings_fields('my_option_group');

        $sections = array(
            'my-setting-general' => 'general',
            'my-setting-style' => 'style',
            'my-setting-content' => 'content'
        );

        foreach($sections as $index => $section) {
            if(isset($_GET['section']) && $_GET['section'] == $section) {
                do_settings_sections($index);
            } else {
                echo "<div style='display:none'>";
                do_settings_sections($index);
                echo "</div>";
            }
        }

        submit_button();
        ?>
    </form>
</div>
<?php
    }

    /**
    * Register and add settings
    */
    public function page_init()
    {
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );



        add_settings_section(
            'general_settings_id', // ID
            'General', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-general' // Page
        );

        add_settings_section(
            'style_settings_id', // ID
            'Style', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-style' // Page
        );

        add_settings_section(
            'content_settings_id', // ID
            'Content', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-content' // Page
        );

        add_settings_field(
            'id_style', // ID
            'Choose style', // Title
            array( $this, 'id_style_callback' ), // Callback
            'my-setting-general', // Page
            'general_settings_id' // Section
        );

        add_settings_field(
            'id_separator', // ID
            'Decimal separators', // Title
            array( $this, 'id_separator_callback' ), // Callback
            'my-setting-general', // Page
            'general_settings_id' // Section
        );

        add_settings_field(
            'id_incl_vat', // ID
            'Show prices incl. VAT', // Title
            array( $this, 'id_incl_vat_callback' ), // Callback
            'my-setting-general', // Page
            'general_settings_id' // Section
        );

        add_settings_field(
            'id_excl_vat', // ID
            'Show prices excl. VAT', // Title
            array( $this, 'id_excl_vat_callback' ), // Callback
            'my-setting-general', // Page
            'general_settings_id' // Section
        );

        add_settings_field(
            'id_primary_color', // ID
            'Primary color', // Title
            array( $this, 'id_primary_color_callback' ), // Callback
            'my-setting-style', // Page
            'style_settings_id' // Section
        );

        add_settings_field(
            'id_secondary_color', // ID
            'Secondary color', // Title
            array( $this, 'id_secondary_color_callback' ), // Callback
            'my-setting-style', // Page
            'style_settings_id' // Section
        );

        add_settings_field(
            'id_title_color', // ID
            'Pop-up title color', // Title
            array( $this, 'id_title_color_callback' ), // Callback
            'my-setting-style', // Page
            'style_settings_id' // Section
        );



        add_settings_field(
            'id_upsell_title_color', // ID
            'Upsell title color', // Title
            array( $this, 'id_upsell_title_color_callback' ), // Callback
            'my-setting-style', // Page
            'style_settings_id' // Section
        );

        add_settings_field(
            'id_title_content', // ID
            'Pop-up title', // Title
            array( $this, 'id_title_content_callback' ), // Callback
            'my-setting-content', // Page
            'content_settings_id' // Section
        );
        add_settings_field(
            'id_upsell_title_content', // ID
            'Upsell title', // Title
            array( $this, 'id_upsell_title_content_callback' ), // Callback
            'my-setting-content', // Page
            'content_settings_id' // Section
        );


        add_settings_field(
            'id_goto_cart_content', // ID
            'Go to cart button', // Title
            array( $this, 'id_goto_cart_content_callback' ), // Callback
            'my-setting-content', // Page
            'content_settings_id' // Section
        );
        add_settings_field(
            'id_close_popup_content', // ID
            'Close pop-up/Continue shopping', // Title
            array( $this, 'id_close_popup_content_callback' ), // Callback
            'my-setting-content', // Page
            'content_settings_id' // Section
        );
        add_settings_field(
            'id_upsell_addtocart_content', // ID
            'Upsell add to cart button', // Title
            array( $this, 'id_upsell_addtocart_content_callback' ), // Callback
            'my-setting-content', // Page
            'content_settings_id' // Section
        );
        add_settings_field(
            'id_upsell_addedtocart_content', // ID
            'Upsell successfully added to cart', // Title
            array( $this, 'id_upsell_addedtocart_content_callback' ), // Callback
            'my-setting-content', // Page
            'content_settings_id' // Section
        );

        add_settings_field(
            'id_upsell_addingtocart_content', // ID
            'Upsell adding to cart', // Title
            array( $this, 'id_upsell_addingtocart_content_callback' ), // Callback
            'my-setting-content', // Page
            'content_settings_id' // Section
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();
        if(isset($input['id_number'])) {
            $new_input['id_number'] = absint($input['id_number']);
        }

        if(isset($input['id_incl_vat'])) {
            $new_input['id_incl_vat'] = $input['id_incl_vat'];
        }

        if(isset($input['id_excl_vat'])) {
            $new_input['id_excl_vat'] = $input['id_excl_vat'];
        }



        if(isset($input['id_style'])) {
            $new_input['id_style'] = sanitize_text_field($input['id_style']);
        }
        if(isset($input['id_title_content'])) {
            $new_input['id_title_content'] = sanitize_text_field($input['id_title_content']);
        }
        if(isset($input['id_upsell_title_content'])) {
            $new_input['id_upsell_title_content'] = sanitize_text_field($input['id_upsell_title_content']);
        }

        if(isset($input['id_goto_cart_content'])) {
            $new_input['id_goto_cart_content'] = sanitize_text_field($input['id_goto_cart_content']);
        }
        if(isset($input['id_close_popup_content'])) {
            $new_input['id_close_popup_content'] = sanitize_text_field($input['id_close_popup_content']);
        }
        if(isset($input['id_upsell_addtocart_content'])) {
            $new_input['id_upsell_addtocart_content'] = sanitize_text_field($input['id_upsell_addtocart_content']);
        }
        if(isset($input['id_upsell_addedtocart_content'])) {
            $new_input['id_upsell_addedtocart_content'] = sanitize_text_field($input['id_upsell_addedtocart_content']);
        }
        if(isset($input['id_upsell_addingtocart_content'])) {
            $new_input['id_upsell_addingtocart_content'] = sanitize_text_field($input['id_upsell_addingtocart_content']);
        }

        if(isset($input['id_primary_color'])) {
            $new_input['id_primary_color'] = $input['id_primary_color'];
        }

        if(isset($input['id_secondary_color'])) {
            $new_input['id_secondary_color'] = $input['id_secondary_color'];
        }

        if(isset($input['id_title_color'])) {
            $new_input['id_title_color'] = $input['id_title_color'];
        }

        if(isset($input['id_upsell_title_color'])) {
            $new_input['id_upsell_title_color'] = $input['id_upsell_title_color'];
        }

        if(isset($input['id_separator'])) {
            $new_input['id_separator'] = $input['id_separator'];
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        echo "";
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_style_callback()
    {
        printf(
            '<select id="id_style" name="my_option_name[id_style]" value="%s">
                <option value="style1" %s>Style 1</option>
                <option value="style2" %s>Style 2</option>
            </select>',
            isset($this->options['id_style']) ? esc_attr($this->options['id_style']) : '',
            isset($this->options['id_style']) && $this->options['id_style'] == 'style1' ? 'selected' : '',
            isset($this->options['id_style']) && $this->options['id_style'] == 'style2' ? 'selected' : '',
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_separator_callback()
    {
        printf(
            '<select id="id_separator" name="my_option_name[id_separator]" value="%s">
                <option value="decimal_comma" %s>Decimal comma</option>
                <option value="decimal_point" %s>Decimal point</option>
            </select>',
            isset($this->options['id_separator']) ? esc_attr($this->options['id_separator']) : '',
            isset($this->options['id_separator']) && $this->options['id_separator'] == 'decimal_comma' ? 'selected' : '',
            isset($this->options['id_separator']) && $this->options['id_separator'] == 'decimal_point' ? 'selected' : '',
        );
    }


    /**
     * Get the settings option array and print one of its values
     */
    public function id_incl_vat_callback()
    {

        printf(
            '<input type="checkbox" id="id_incl_vat" name="my_option_name[id_incl_vat]" %s />',
            isset($this->options['id_incl_vat']) && $this->options['id_incl_vat'] == 'on' ? 'checked' : ''
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_excl_vat_callback()
    {

        printf(
            '<input type="checkbox" id="id_excl_vat" name="my_option_name[id_excl_vat]" %s />',
            isset($this->options['id_excl_vat']) && $this->options['id_excl_vat'] == 'on' ? 'checked' : ''
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_primary_color_callback()
    {

        printf(
            '<input name="my_option_name[id_primary_color]" class="primary-color-field" type="text" value="%s" data-default-color="#effeff" />',
            isset($this->options['id_primary_color']) ? $this->options['id_primary_color'] : "#effeff"
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_secondary_color_callback()
    {

        printf(
            '<input name="my_option_name[id_secondary_color]" class="secondary-color-field" type="text" value="%s" data-default-color="#effeff" />',
            isset($this->options['id_secondary_color']) ? $this->options['id_secondary_color'] : "#effeff"
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_title_color_callback()
    {

        printf(
            '<input name="my_option_name[id_title_color]" class="title-color-field" type="text" value="%s" data-default-color="#effeff" />',
            isset($this->options['id_title_color']) ? $this->options['id_title_color'] : "#effeff"
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_upsell_title_color_callback()
    {

        printf(
            '<input name="my_option_name[id_upsell_title_color]" class="upsell-title-color-field" type="text" value="%s" data-default-color="#effeff" />',
            isset($this->options['id_upsell_title_color']) ? $this->options['id_upsell_title_color'] : "#effeff"
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_title_content_callback()
    {

        printf(
            '<input name="my_option_name[id_title_content]" class="id_title_content-field" type="text" value="%s"  />',
            isset($this->options['id_title_content']) ? $this->options['id_title_content'] : ""
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_upsell_title_content_callback()
    {

        printf(
            '<input name="my_option_name[id_upsell_title_content]" class="id_upsell_title_content-field" type="text" value="%s"  />',
            isset($this->options['id_upsell_title_content']) ? $this->options['id_upsell_title_content'] : ""
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_goto_cart_content_callback()
    {

        printf(
            '<input name="my_option_name[id_goto_cart_content]" class="id_goto_cart_content-field" type="text" value="%s"  />',
            isset($this->options['id_goto_cart_content']) ? $this->options['id_goto_cart_content'] : "Go to cart"
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_close_popup_content_callback()
    {

        printf(
            '<input name="my_option_name[id_close_popup_content]" class="id_close_popup_content-field" type="text" value="%s"  />',
            isset($this->options['id_close_popup_content']) ? $this->options['id_close_popup_content'] : "Continue shopping"
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_upsell_addtocart_content_callback()
    {

        printf(
            '<input name="my_option_name[id_upsell_addtocart_content]" class="id_upsell_addtocart_content-field" type="text" value="%s"  />',
            isset($this->options['id_upsell_addtocart_content']) ? $this->options['id_upsell_addtocart_content'] : "Add to cart"
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_upsell_addedtocart_content_callback()
    {

        printf(
            '<input name="my_option_name[id_upsell_addedtocart_content]" class="id_upsell_addedtocart_content-field" type="text" value="%s"  />',
            isset($this->options['id_upsell_addedtocart_content']) ? $this->options['id_upsell_addedtocart_content'] : "Added to cart"
        );
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function id_upsell_addingtocart_content_callback()
    {

        printf(
            '<input name="my_option_name[id_upsell_addingtocart_content]" class="id_upsell_addingtocart_content-field" type="text" value="%s"  />',
            isset($this->options['id_upsell_addingtocart_content']) ? $this->options['id_upsell_addingtocart_content'] : "Adding.."
        );
    }


    public function get_options()
    {
        return $this->options;
    }


}
