<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
<?php
function setup_woocommerce_sample_products() {
    // ✅ Check if WooCommerce is already active
    if (!class_exists('WooCommerce')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $plugin_slug = 'woocommerce';
        $plugin_file = 'woocommerce/woocommerce.php';
        $plugin_path = WP_PLUGIN_DIR . '/woocommerce';

        // ✅ Install WooCommerce if not present
        if (!file_exists($plugin_path)) {
            $upgrader = new Plugin_Upgrader();
            $upgrader->install("https://downloads.wordpress.org/plugin/$plugin_slug.latest-stable.zip");
        }

        // ✅ Activate WooCommerce if installed
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file) && !is_plugin_active($plugin_file)) {
            activate_plugin($plugin_file);
        }
    }

    // ✅ Create WooCommerce Default Pages
    if (class_exists('WC_Install')) {
        WC_Install::create_pages();
    }

    // ✅ Create Sample Category
    $category_name = 'Sample Category';
    if (!term_exists($category_name, 'product_cat')) {
        wp_insert_term($category_name, 'product_cat');
    }

    // ✅ Define 10 Unique Sample Products
    $sample_products = [
        ["Gaming Mouse", "High-speed gaming mouse with RGB lighting."],
        ["Wireless Keyboard", "Ergonomic wireless keyboard with mechanical switches."],
        ["Bluetooth Speaker", "Portable Bluetooth speaker with deep bass."],
        ["Smartwatch", "Fitness tracking smartwatch with heart rate monitor."],
        ["USB-C Hub", "Multi-port USB-C hub for laptops and tablets."],
        ["Gaming Headset", "Surround sound gaming headset with noise cancellation."],
        ["Wireless Earbuds", "True wireless earbuds with long battery life."],
        ["Laptop Stand", "Adjustable laptop stand for ergonomic work setup."],
        ["External Hard Drive", "1TB external hard drive for backup storage."],
        ["4K Monitor", "27-inch 4K UHD monitor with HDR support."]
    ];

    // ✅ Add Products with Unique Images
    foreach ($sample_products as $index => $product) {
        $product_name = $product[0];
        $product_description = $product[1];

        // Check if product already exists
        $existing_product = get_page_by_title($product_name, OBJECT, 'product');
        if ($existing_product) continue;

        $post_id = wp_insert_post([
            'post_title'   => $product_name,
            'post_content' => $product_description,
            'post_status'  => 'publish',
            'post_type'    => 'product',
        ]);

        if ($post_id) {
            wp_set_object_terms($post_id, $category_name, 'product_cat');
            update_post_meta($post_id, '_regular_price', rand(50, 500));
            update_post_meta($post_id, '_price', rand(50, 500));
            update_post_meta($post_id, '_stock_status', 'instock');

            // ✅ Add a Unique Image for Each Product
            $image_url = "https://picsum.photos/600/600?random=" . ($index + 1);
            $image_id = upload_product_image($image_url);
            if ($image_id) {
                set_post_thumbnail($post_id, $image_id);
            }
        }
    }

    // ✅ Enable Elementor WooCommerce Compatibility
    update_option('elementor_woocommerce_support', 'yes');
}

// ✅ Function to Upload Images from URL
function upload_product_image($image_url) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = download_url($image_url);
    $file_array = [
        'name'     => basename($image_url) . '.jpg',
        'tmp_name' => $tmp
    ];

    // Handle upload
    $id = media_handle_sideload($file_array, 0);

    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }

    return $id;
}

add_action('admin_init', 'setup_woocommerce_sample_products');
