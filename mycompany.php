<?php

$ubricks_page = 'mycompany.php'; // это часть URL страницы, используется строковое значение, т.к. в данном случае не будет зависимости от того, в какой файл вы всё это вставите

/**
 * Функция добавляет страницу настроек в консоль
 */
function ubricks_options() {
	global $ubricks_page;
	add_menu_page( 'О компании', 'О компании', 'manage_options', $ubricks_page, 'ubricks_option_page', 'dashicons-store', '2');  
}
add_action('admin_menu', 'ubricks_options');


/**
 * Возвратная функция (Callback)
 */ 
function ubricks_option_page(){
	global $ubricks_page;
	?><div class="wrap">
		<h2>О компании</h2>
		<form method="post" enctype="multipart/form-data" action="options.php">
			<?php 
			settings_fields('ubricks_options');
			do_settings_sections($ubricks_page);
			?>
			<p class="submit">  
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
			</p>
		</form>
	</div><?php
}


/*
 * Регистрируем настройки
 * Настройки хранятся в базе под названием ubricks_options
 */
function ubricks_option_settings() {
	global $ubricks_page;
	
	// Присваиваем функцию валидации ( ubricks_validate_settings() ). Вы найдете её ниже
	register_setting( 'ubricks_options', 'ubricks_options', 'ubricks_validate_settings' );
 
	// Первая секция
	add_settings_section( 'ubricks_section_1', 'Контакты', '', $ubricks_page );

		// Текстовое поле «Наименование» в ubricks_section_1
		$ubricks_field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mycompany-name',
			'desc'      => 'Например, ООО «Победа»',
		);
		add_settings_field( 'mycompany-name', 'Наименование', 'ubricks_option_display_settings', $ubricks_page, 'ubricks_section_1', $ubricks_field_params );
	
	
		// Текстовое поле «Email» в ubricks_section_1
		$ubricks_field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mycompany-email',
			'desc'      => '',
		);
		add_settings_field( 'mycompany-email', 'Email', 'ubricks_option_display_settings', $ubricks_page, 'ubricks_section_1', $ubricks_field_params );

	
		// Текстовое поле «Телефон» в ubricks_section_1
		$ubricks_field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mycompany-phone',
			'desc'      => '',
		);
		add_settings_field( 'mycompany-phone', 'Телефон', 'ubricks_option_display_settings', $ubricks_page, 'ubricks_section_1', $ubricks_field_params );
	

		// Текстовое поле «Режим работы» в ubricks_section_1
		$ubricks_field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mycompany-time',
			'desc'      => '',
		);
		add_settings_field( 'mycompany-time', 'Режим работы', 'ubricks_option_display_settings', $ubricks_page, 'ubricks_section_1', $ubricks_field_params );


		// Текстовое поле «Город» в ubricks_section_1
		$ubricks_field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mycompany-city',
			'desc'      => 'Населённый пункт, например — «г. Краснодар»', // описание
		);
		add_settings_field( 'mycompany-city', 'Город', 'ubricks_option_display_settings', $ubricks_page, 'ubricks_section_1', $ubricks_field_params );

	
		// Текстовое поле «Адрес» в ubricks_section_1
		$ubricks_field_params = array(
			'type'      => 'text', // тип
			'id'        => 'mycompany-address',
			'desc'      => '',
		);
		add_settings_field( 'mycompany-address', 'Адрес', 'ubricks_option_display_settings', $ubricks_page, 'ubricks_section_1', $ubricks_field_params );
		
	
	
}
add_action( 'admin_init', 'ubricks_option_settings' );


/*
 * Функция отображения полей ввода
 * Здесь задаётся HTML и PHP, выводящий поля
 */
function ubricks_option_display_settings($args) {
	extract( $args );
 
	$option_name = 'ubricks_options';
 
	$o = get_option( $option_name );
 
	switch ( $type ) {  
		case 'text':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
		break;
		case 'textarea':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<textarea class='code large-text' cols='50' rows='10' type='text' id='$id' name='" . $option_name . "[$id]'>$o[$id]</textarea>";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
		break;
		case 'checkbox':
			$checked = ($o[$id] == 'on') ? " checked='checked'" :  '';  
			echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked /> ";  
			echo ($desc != '') ? $desc : "";
			echo "</label>";  
		break;
		case 'select':
			echo "<select id='$id' name='" . $option_name . "[$id]'>";
			foreach($vals as $v=>$l){
				$selected = ($o[$id] == $v) ? "selected='selected'" : '';  
				echo "<option value='$v' $selected>$l</option>";
			}
			echo ($desc != '') ? $desc : "";
			echo "</select>";  
		break;
		case 'radio':
			echo "<fieldset>";
			foreach($vals as $v=>$l){
				$checked = ($o[$id] == $v) ? "checked='checked'" : '';  
				echo "<label><input type='radio' name='" . $option_name . "[$id]' value='$v' $checked />$l</label><br />";
			}
			echo "</fieldset>";  
		break; 
	}
}


/*
 * Функция проверки правильности вводимых полей
 */
function ubricks_validate_settings($input) {
	foreach($input as $k => $v) {
		$valid_input[$k] = trim($v);
 
		/* Вы можете включить в эту функцию различные проверки значений, например
		if(! задаем условие ) { // если не выполняется
			$valid_input[$k] = ''; // тогда присваиваем значению пустую строку
		}
		*/
	}
	return $valid_input;
}


/*
 * Шоткоды вывода полей
 */

// Название компании [mycompany-name]
add_shortcode( 'mycompany-name', 'get_mycompany_name' );
function get_mycompany_name() {
	$all_options = get_option('ubricks_options');
	return $all_options['mycompany-name'];
}


// Электронная почта [mycompany-email]
add_shortcode( 'mycompany-email', 'get_mycompany_email' );
function get_mycompany_email() {
	$all_options = get_option('ubricks_options');
	$mycompany_email = '<a href="mailto:' . $all_options['mycompany-email'] . '">' . $all_options['mycompany-email'] . '</a>';
	return $mycompany_email;
}


// Телефон [mycompany-phone]
add_shortcode( 'mycompany-phone', 'get_mycompany_phone' );
function get_mycompany_phone() {
	$all_options = get_option('ubricks_options');
	// <a href="mailto:contact@usota.ru">contact@usota.ru</a>
	$mycompany_phone = '<a href="tel:' . $all_options['mycompany-phone'] . '">' . $all_options['mycompany-phone'] . '</a>';
	return $mycompany_phone;
}


// Режим работы [mycompany-time]
add_shortcode( 'mycompany-time', 'get_mycompany_time' );
function get_mycompany_time() {
	$all_options = get_option('ubricks_options');
	return $all_options['mycompany-time'];
}


// Город [mycompany-city]
add_shortcode( 'mycompany-city', 'get_mycompany_city' );
function get_mycompany_city() {
	$all_options = get_option('ubricks_options');
	return $all_options['mycompany-city'];
}


// Адрес [mycompany-address]
add_shortcode( 'mycompany-address', 'get_mycompany_address' );
function get_mycompany_address() {
	$all_options = get_option('ubricks_options');
	return $all_options['mycompany-address'];
}