<?php

/*
Plugin Name: Pop-up add to cart
Description: Shows a pop-up after adding items to cart
Version: 1.0
Author: Niels Vermeiren
*/

class PopupUpsellCart
{
    public function __construct()
    {
        add_filter('woocommerce_add_cart_item_data', array($this, 'addedToCartListener'), 10, 4);
        add_action('wp_enqueue_scripts', array($this, 'themeslug_enqueue_script'));
        add_action("wp_ajax_get_productinfo", array($this,"get_productinfo"));
        add_action("wp_ajax_nopriv_get_productinfo", array($this,"get_productinfo"));
        add_action("wp_ajax_clear_sesh", array($this, "clear_sesh"));
        add_action("wp_ajax_nopriv_clear_sesh", array($this,"clear_sesh"));
    }

    public function addedToCartListener($cart_item_data, $product_id, $variation_id, $quantity)
    {
        session_start();
        $headProduct = $_SESSION['headproduct'] ?? null ;
        $otherProducts = $_SESSION['otheritemsincart'] ?? null;
        $currentProduct = wc_get_product($product_id);

        if ($currentProduct->get_type() == "woosg") {
            //No parent grouped product in basket/pop-up
            return;
        }

        if (!$headProduct) {
            //We are dealing with the head product
            $_SESSION['headproduct'] = array(
                "product_id" => $product_id,
                "quantity"   => $quantity
            );
        } else {

            //We already have a head product, so this is an extra product
            $products = array();
            if ($otherProducts) {
                $products = $otherProducts;
            }
            //Add this product to the session
            $products[] = array(
                "product_id" => $product_id,
                "quantity"   => $quantity
            );

            $_SESSION['otheritemsincart'] = $products;

        }
        return $cart_item_data;
    }

    public function themeslug_enqueue_script()
    {
        session_start();
        global $woocommerce;
        $upsellsShow = array();
        $products = array();

        //Scripts
        wp_enqueue_style('styleicons', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css", false);
        wp_enqueue_style('style', plugin_dir_url(__FILE__) . "css/bootstrap.min.css", false);
        wp_enqueue_script('jsboot', plugin_dir_url(__FILE__) . "js/bootstrap.min.js", false);
        wp_enqueue_style('style1', plugin_dir_url(__FILE__) . "css/style1.css", false);
        wp_enqueue_script('jsmodal', plugin_dir_url(__FILE__) . "js/bootstrap-show-modal.js", false);
        wp_enqueue_script('jscookie', plugin_dir_url(__FILE__) . "js/jquery.cookie.min.js", false);
        wp_enqueue_script('jslanpopup', plugin_dir_url(__FILE__) . "js/main.js", false);

        if(isset($_SESSION['headproduct'])) {
            //get head cart item info
            if(!isset($_SESSION['headproduct'])) {
                return;
            }
            $head_product_obj = $_SESSION['headproduct'];


            if($head_product_obj) {
                $head_product = wc_get_product($head_product_obj['product_id']);
                $name = $head_product->get_name();
                $price = $head_product->get_price();
                $product_image_url = "";
                if(wp_get_attachment_image_src(get_post_thumbnail_id($head_product->get_id()), 'single-post-thumbnail')) {
                    $product_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($head_product->get_id()), 'single-post-thumbnail')[0];
                }

                $products[] = array(
                    "id" => $head_product_obj['product_id'],
                    "name" => $name,
                    "price" => $price,
                    "image" => $product_image_url,
                    "quantity" => $head_product_obj['quantity']
                );

                //Other items in cart



                if(isset($_SESSION['otheritemsincart'])) {
                    $items_in_cart_obj = $_SESSION['otheritemsincart'];
                    foreach($items_in_cart_obj as $item) {
                        $items_in_cart = wc_get_product($item['product_id']);
                        $name = $items_in_cart->get_name();
                        $price = $items_in_cart->get_price();
                        $product_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($items_in_cart->get_id()), 'single-post-thumbnail')[0];

                        $products[] = array(
                            "id" => $item['product_id'],
                            "name" => $name,
                            "price" => $price,
                            "image" => $product_image_url,
                            "quantity" => $item['quantity']
                        );
                    }
                }

                //get upsells
                $upsells = $head_product->get_upsell_ids();
                $upsells = array_slice($upsells, 0, 3);
                foreach ($upsells as $key => $upsell) {
                    $upsellProd = wc_get_product($upsell);
                    $upsellName  = $upsellProd->get_name();
                    $upsellPrice = $upsellProd->get_price();
                    $upsellImageUrl = "";
                    if(wp_get_attachment_image_src(get_post_thumbnail_id($upsell), 'single-post-thumbnail')) {
                        $upsellImageUrl = wp_get_attachment_image_src(get_post_thumbnail_id($upsell), 'single-post-thumbnail')[0];
                    }
                    $link = get_permalink($upsell);
                    $upsellsShow[] = array(
                        'id' => $upsell,
                        'name' => $upsellName,
                        'price' => $upsellPrice,
                        'image' => $upsellImageUrl,
                        'link' => $link
                    );
                }
            }
        }

        //Variables
        wp_localize_script("jslanpopup", "products", $products);
        wp_localize_script("jslanpopup", "upsells", $upsellsShow);
        wp_localize_script("jslanpopup", "is_product_page", is_product());
        wp_localize_script('jslanpopup', 'ajax_object', array( 'ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function get_productinfo()
    {

        $product_id = $_POST['product_id'];

        $product2 = wc_get_product($product_id);
        $name = $product2->get_name();
        $price = $product2->get_price();
        $product_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($product2->get_id()), 'single-post-thumbnail')[0];

        //get upsells
        $upsells = $product2->get_upsell_ids('edit');
        $upsells = array_slice($upsells, 0, 3);
        $upsellsShow = array();

        foreach ($upsells as $key => $upsell) {
            $upsellProd = wc_get_product($upsell);
            $upsellName  = $upsellProd->get_name();
            $upsellPrice = $upsellProd->get_price();
            $upsellImageUrl = wp_get_attachment_image_src(get_post_thumbnail_id($upsell), 'single-post-thumbnail')[0];
            $link = get_permalink($upsell);
            $upsellsShow[] = array(
                'id' => $upsell,
                'name' => $upsellName,
                'price' => $upsellPrice,
                'image' => $upsellImageUrl,
                'link' => $link
            );
        }

        $add_to_cart = $_GET['add-to-cart'];

        $res_array = array(
            'name' => $name,
            'price' => $price,
            'image' =>  $product_image_url,
            'upsells' => $upsellsShow
        );

        echo json_encode($res_array);

        wp_die();
    }

    //Clear the session of the user
    public function clear_sesh()
    {
        session_start();
        unset($_SESSION['headproduct']);
        unset($_SESSION['otheritemsincart']);
        echo 'success';
        wp_die();
    }
}

$popupUpsellCart = new PopupUpsellCart();



//Ajax endpoint for the products which can be added from the category pages


//Ajax endpoint for clearing the session, after clicking on the add-to-cart btn
