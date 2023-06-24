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
                href='?page=upsell-popup-options&section=style'>Style</a></div>
        <?php
        settings_fields('my_option_group');
        if(isset($_GET['section']) && $_GET['section'] == 'general') {
            echo "<div style='display:none'>";
            do_settings_sections('my-setting-style');
            echo "</div>";
            do_settings_sections('my-setting-general');
        }

        if(isset($_GET['section']) && $_GET['section'] == 'style') {
            echo "<div style='display:none'>";
            do_settings_sections('my-setting-general');
            echo "</div>";
            do_settings_sections('my-setting-style');
        }

        if(!isset($_GET['section'])) {
            echo "<div style='display:none'>";
            do_settings_sections('my-setting-style');
            echo "</div>";
            do_settings_sections('my-setting-general');
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
            'Style settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-style' // Page
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
            'id_border_radius', // ID
            'Border radius (%)', // Title
            array( $this, 'id_border_radius_callback' ), // Callback
            'my-setting-style', // Page
            'style_settings_id' // Section
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

        if(isset($input['id_border_radius'])) {
            $new_input['id_border_radius'] = $input['id_border_radius'];
        }


        return $new_input;
    }

    public function sanitize_style($input)
    {
        $new_input = array();
        if(isset($input['id_border_radius'])) {
            $new_input['id_border_radius'] = $input['id_border_radius'];
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
    public function id_border_radius_callback()
    {

        printf(
            '<input type="number" id="id_border_radius" name="my_option_name[id_border_radius]" value="%s" />',
            isset($this->options['id_border_radius']) ? $this->options['id_border_radius'] : 0
        );
    }


}

$options = new OptionsPage();
