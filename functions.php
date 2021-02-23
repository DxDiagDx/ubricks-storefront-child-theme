<?php

/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */
function sf_child_theme_dequeue_style() {
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
}

/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */






/*
 * Add mobile handheld sidebar
 */
add_action( 'woocommerce_before_shop_loop', 'handheld_sidebar', 40 );
function handheld_sidebar() {
	echo '<div class="handheld-sidebar-toggle">
			<button class="btn sidebar-toggler" type="button">
				<i class="fas fa-sliders-h"></i>
				<span>Фильтры</span>
			</button>
		</div>';
}

add_action( 'wp_enqueue_scripts', 'handheld_sidebar_script' );
function handheld_sidebar_script() {
    wp_enqueue_script( 'handheld_sidebar_script', get_stylesheet_directory_uri() . '/js/sidebar-mobile.js', array(), NULL, true);
}



/* Подключаем страницу с настройками "О комании" */
require_once( dirname(__FILE__) . '/mycompany.php');

// url сайта [site_url] и имя сайта [site_name]//
add_action( 'init', function() {

	add_shortcode( 'site_url', function( $atts = null, $content = null ) {
		return site_url();
	} );
	
	add_shortcode( 'site_name', function( $atts = null, $content = null ) {
		return get_bloginfo( 'name' );
	} );	

} );


/* Выводим в шапке контакты */ 
add_action( 'storefront_header', 'usota_storefront_header_content', 30 );
function usota_storefront_header_content() { 
	$all_options = get_option('ubricks_options');
	$mycompany_phone = '<a href="tel:' . $all_options['mycompany-phone'] . '">' . $all_options['mycompany-phone'] . '</a>';
	?>
	<div class="header-info hide-on-tablet hide-on-mobile">
		<div class="header-info-phone"><?php echo $mycompany_phone ?></div>
		<span><?php echo $all_options['mycompany-time'] ?></span>
	</div>
	<?php
}


/* Переносим поиск в середину шапки */
add_action('init', 'remove_storefront_header_search_init');
function remove_storefront_header_search_init() {
	remove_action('storefront_header', 'storefront_product_search', 40);
}
add_action( 'storefront_header', 'storefront_product_search', 25 );





/* Размер изображения в карточке товара: установить 768px */
add_action( 'storefront_woocommerce_setup', 'mytheme_add_woocommerce_single_image_width' );
function mytheme_add_woocommerce_single_image_width() {
	add_theme_support( 'woocommerce', array(
        	'single_image_width' => 768,
        ) 
    );
}


/* Добавляем маску ввода номера телефона на странице оформления заказа */
add_action('wp_enqueue_scripts', 'my_maskedinput');
function my_maskedinput() {
    if (is_checkout()) {
        wp_enqueue_script('maskedinput', get_stylesheet_directory_uri() . '/js/jquery.maskedinput.js', array('jquery'));
        add_action( 'wp_footer', 'masked_script', 999);
    }
}
function masked_script() {
    if ( wp_script_is( 'jquery', 'done' ) ) {
?>
    <script type="text/javascript">
        jQuery( function( $ ) {
            $("#billing_phone").mask("+7 999 999-99-99");
        });
    </script>
<?php
    }
}


/**
 * Сопутствующие товары в 4 колонки
 */
add_filter( 'woocommerce_output_related_products_args', 'usota_rel_products_args', 25 );
function usota_rel_products_args( $args ) {
	$args[ 'posts_per_page' ] = 4; // отображаемое количество
	$args[ 'columns' ] = 4; // количество колонок
	return $args;
}

/**
 * Апсейлы в 4 колонки
 */
add_filter( 'woocommerce_upsell_display_args', 'wc_change_number_related_products', 20 );
function wc_change_number_related_products( $args ) {
	$args['posts_per_page'] = 4; // отображаемое количество
 	$args['columns'] = 4; // количество колонок
 	return $args;
}


/* Добавить ссылку на контакты в футербар на мобильных */
add_filter( 'storefront_handheld_footer_bar_links', 'company_add_contact_link' );
function company_add_contact_link( $links ) {
	$new_links = array(
		'contact' => array(
			'priority' => 60,
			'callback' => 'company_contact_link',
		),
	);

	$links = array_merge( $new_links, $links );

	return $links;
}

function company_contact_link() {
	echo '<a href="' . esc_url( '/kontakty' ) . '">' . __( 'Контакты' ) . '</a>';
}


/* Мета-поля для категорий товаров Woocommerce */
add_action('product_cat_edit_form_fields', 'impuls_meta_product_cat', 20);
function impuls_meta_product_cat($term){
?>
<tr class="form-field">
	<th scope="row" valign="top"><label>Заголовок h1</label></th>
	<td>
		<input type="text" name="impuls[h1]" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'h1', 1 ) ) ?>"><br />
		<p class="description">Заголовок страницы</p>
	</td>
</tr>
<?php
}


/* Сохранение данных в БД */
add_action('edited_product_cat', 'impuls_save_meta_product_cat');  
add_action('create_product_cat', 'impuls_save_meta_product_cat');
	function impuls_save_meta_product_cat($term_id){
		if (!isset($_POST['impuls']))
	return;
	$impuls = array_map('trim', $_POST['impuls']);
	foreach($impuls as $key => $value){
		if(empty($value)){
			delete_term_meta($term_id, $key);
			continue;
		}
		update_term_meta($term_id, $key, $value);
	}

	return $term_id;
}

/* Вывод h1 для категорий товаров  Woocommerce */
if(strpos($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], '/product-category/'))
add_filter ( 'woocommerce_show_page_title' , 'impuls_woocommerce_product_cat_h1' , 10 , 2 ); 
function impuls_product_cat_h1(){
	$pch = get_term_meta (get_queried_object()->term_id, 'h1', true);
	echo '<h1 class="woocommerce-products-header__title page-title">'.$pch.'</h1>';
	
	if(empty($pch)){
		echo	'<h1 class="woocommerce-products-header__title page-title">'.get_queried_object()->name.'</h1>';
	}
}

function impuls_woocommerce_product_cat_h1(){ 
	return  impuls_product_cat_h1($pch);     
}


/* Футер */
add_action( 'init', 'custom_remove_footer_credit', 10 );
function custom_remove_footer_credit () {
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    add_action( 'storefront_footer', 'custom_storefront_credit', 20 );
}
function custom_storefront_credit() {
    ?>
    <div class="site-info">
		<div class="copyright">&copy; <?php echo get_the_date( 'Y' ).' '.get_bloginfo( 'name' ); ?></div>
		<div class="info_dev">Сделано в <a href="https://usota.ru">Юсоте</a></div>
    </div>
	<!-- .site-info -->
    <?php
}