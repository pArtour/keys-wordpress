<?php 

function keys_add_title() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support( 'customize-selective-refresh-widgets' );
}

add_action('after_setup_theme', 'keys_add_title');

function keys_menus() {
    $locations = array(
        'primary' => "Main pages menu",
        'secondary' => "Categories menu",
        'footer' => "Footer menu",
    );

    register_nav_menus($locations);
}

add_action('init', 'keys_menus');

add_filter( 'nav_menu_link_attributes', 'filter_nav_menu_link_attributes', 10, 3 );

function filter_nav_menu_link_attributes($atts, $item, $args) {
    if ($args->menu === 'primary') {
        $atts['class'] = 'menu__link';
        if ($atts['href'] === '#') {
            $atts['class'] .= ' js-open-popup';
            $atts['href'] = '';
        }
        if($item->post_title === 'Скачать каталог') {
            $atts['data-modal']='catalog';
        }
        if($item->post_title === 'Прайс-лист'){
            $atts['data-modal']='call-price';
        }
    };
    if ($args->menu === 'footer') {
        $atts['class'] = '';
    };
    return $atts;
}

add_action('wp_enqueue_scripts', 'keys_scripts');

add_filter( 'excerpt_length', function(){
	return 40;
} );
add_filter('excerpt_more', function($more) {
	return '...';
});
function keys_scripts() {
    wp_enqueue_style( 'keys-style', get_stylesheet_uri());
    wp_enqueue_script( 'keys-scripts', get_template_directory_uri(  ) . '/scripts/script.js', array(), null, true);
    wp_enqueue_script( 'keys-jquery-func', get_template_directory_uri(  ) . '/scripts/jquery-func.js', array('jquery'), '1.0', true);
    wp_enqueue_script( 'keys-footer', get_template_directory_uri(  ) . '/scripts/footer.js', array(), null, true);
    wp_enqueue_script( 'keys-loadmore', get_template_directory_uri(  ) . '/scripts/loadmore.js', array(), null, true);
    wp_enqueue_script( 'keys-swiper-bundle', get_template_directory_uri(  ) . '/scripts/swiper-bundle.js', array(), null, true);
    wp_enqueue_script( 'keys-swiper', get_template_directory_uri(  ) . '/scripts/swiper.js', array(), null, true);
    wp_enqueue_script( 'keys-cart', get_template_directory_uri(  ) . '/scripts/cart-ajax-add.js', array('jquery'), '1.0', true);
    wp_enqueue_script( 'keys-cart-scripts', get_template_directory_uri(  ) . '/scripts/cart.js', array(), null, true);
    wp_enqueue_script( 'filters-scripts', get_template_directory_uri(  ) . '/scripts/filter.js', array('jquery'), '1.0', true);
    wp_enqueue_script( 'filters-pagination', get_template_directory_uri(  ) . '/scripts/pagination.js', array('jquery'), '1.0', true);
    wp_enqueue_script( 'feedback', get_stylesheet_directory_uri() . '/scripts/feedback.js', array( 'jquery' ), 1.0, true );  
    wp_enqueue_script( 'reviews_more', get_stylesheet_directory_uri() . '/scripts/review.js', array( 'jquery' ), 1.0, true );  
};


add_theme_support( 'custom-logo' );
add_theme_support( 'woocommerce' );


function arphabet_widgets_init() {

    register_sidebar( array(
        'name' => 'Home right sidebar',
        'id' => 'home_right_1',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet_widgets_init' );

function motique_widgets_init() {

    register_sidebar( array(
      'name'          => 'Home right sidebar',
      'id'            => 'home_right_1',
      'before_widget' => '<div>',
      'after_widget'  => '</div>',
      'before_title'  => '<h2 class="rounded">',
      'after_title'   => '</h2>',
    ) );
    register_sidebar( array(
        'name'          => 'Home right sidebar',
        'id'            => 'home_right_2',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="rounded">',
        'after_title'   => '</h2>',
      ) );

  }
  add_action( 'widgets_init', 'motique_widgets_init' );
  add_theme_support( 'wc-product-gallery-slider' );



function change_cart_data(){
    $custom_data = array();
    $product_num = sanitize_text_field( $_POST['product_num'] );
    $product_color = $_POST['product_color'];
    $product_id = $_POST['id'];

    $custom_data['custom_data']['color'] = $product_color;
    
    if( isset($custom_data['custom_data']) && sizeof($custom_data['custom_data']) > 0 && $product_id > 0 ) {
        $product = wc_get_product( $product_id );
    }
    
    WC()->cart->add_to_cart( $product_id, $product_num, '0', array(), $custom_data );
    echo WC()->cart->get_cart_contents_count();
}
   
add_action('wp_ajax_cart-data', 'change_cart_data');
add_action('wp_ajax_nopriv_cart-data', 'change_cart_data');


function change_cart_data_delete(){
    if(! WC()->cart->is_empty()) {
        $custom_data = array();
        $product_num = sanitize_text_field( $_POST['product_num'] );
        $product_color = $_POST['product_color'];
        $product_id = $_POST['id'];

        $custom_data['custom_data']['color'] = $product_color;

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                WC()->cart->remove_cart_item($cart_item_key);
                WC()->cart->add_to_cart( $product_id, $product_num, '0', array(), $custom_data );
                echo WC()->cart->get_cart_contents_count();
            }
        }
    }
}
   
add_action('wp_ajax_cart-data-delete', 'change_cart_data_delete');
add_action('wp_ajax_nopriv_cart-data-delete', 'change_cart_data_delete');


function change_cart_filter_data(){
    
    
    $providers = $_POST['product_providers'];
    $url = $_POST['url'];
    $index = $_POST['index'];
    $paged = (int)$index;

    $providers_array = array();
    foreach($providers as $key => $value) {
        array_push($providers_array, $value);
    }

    $search = $_POST['search'];
    $query = unserialize(stripslashes($_POST['query']));   
    $args = array (
        "s" => $query["s"],
        "post_type" => $query["post_type"],
        "paged"     => $paged,
        'posts_per_page' => 2
    );

    $parts = explode("/", $url);
    $slug = $parts[count($parts) - 2];

    if ($slug == 'katalog') {
        $slug = '';
    }

    if ($search) {
        $products = new WP_Query( $args );
        // $products['paged'] = $paged;
    } else {
        if (count($providers_array) > 0) {
            $products = new WP_Query( array(
                'post_type'      => 'product',
                'product_cat'    => $slug,
                'post_status'    => 'publish',
                'posts_per_page' => 2,
                'paged'          => $paged,
                'tax_query'      => array( array(
                     'taxonomy'        => 'pa_provider',
                     'field'           => 'slug',
                     'terms'           =>  $providers_array,
                     'operator'        => 'IN',
                 ) )
             ) );
        } else {
            $products = new WP_Query( array(
                'post_type' => 'product',
                'product_cat'    =>  $slug,
                'posts_per_page' => 2,
                'paged' => $paged
            )); 
        }
    }

    if ( $products->have_posts()) {
        ?> <ul class="catalog__list catalog__list--mobile"> <?php
        while ( $products->have_posts() ) { $products->the_post(); ?>
            <?php global $product; ?>
            <div class="page-catalog-popup popup closed" id="in-cart" data-modal="in-cart">
                    <button class="popup__close js-close"></button>
                    <h2 class="popup__title popup__title_l">Товар добавлен в корзину</h2>
                    <div class="page-catalog-popup__wrapper">
                        <img src="<?php the_post_thumbnail_url(  ); ?>" alt="<?php the_title(); ?>" class="popup-catalog__img">
                        <div class="catalog__info popup-catalog__info">
                            <h4><?php the_title(); ?> </h4>
                            <p class="catalog__producer">Производитель:<?php the_field('proizvoditel'); ?></p>
                            <p class="catalog__producer">Арт. <?php the_field('artikul'); ?></p>
                        </div>

                    </div>
                    <div class="error__buttons page-catalog-popup__buttons">
                        <a href="<?php echo get_page_link( 7 ); ?>" data-product-id="<?php echo $product->get_id(); ?>" class="add-to-cart-confirm error__button button_orange">Оформить заказ</a>
                        <button type="button" class="error__button button_white page-catalog-popup-close" href="">Продолжить покупки</button>
                    </div>
                </div>

                <li class="catalog__item card-id catalog__item--20" data-product-id="<?echo $product->get_id();?>">
                    
                    <a href="<?php the_permalink(); ?>" class="catalog__link">
                        <img class="catalog__img" src="<?php the_post_thumbnail_url(  ); ?>" alt=" <?php the_title(); ?>">
                        <div class="catalog__info">
                            <h4 class="text__link"> <?php the_title(); ?></h4>
                            <p class="catalog__artikul"><?php the_field('artikul'); ?> </p>
                            <p class="catalog__producer">Производитель:</br><?php the_field('proizvoditel'); ?></p>
                        </div>
                        <button data-product-id="<?php echo $product->get_id(); ?>" class="catalog-item-btn button_orange catalog__button catalog-js-open-popup" data-modal="in-cart">В корзину</button>
                    </a>
                </li>
        <?php }
        ?> </ul>
        <?php
        wp_reset_query(  );
        ?>
        <ul class="catalog__pagination" data-max="<?php echo $products->max_num_pages; ?>">
            <li><button type="button" class="pagination__arr pag_prev">&lsaquo;&emsp;</button></li>

            <?php
            
            for($i = 1; $i <= $products->max_num_pages; $i++) {
                ?>
                <li><button data-index="<?php echo $i; ?>" data-current="<?php echo $paged; ?>" class="pagination__button"><?php echo $i; ?></button></li>
                <?php
            }
            
            ?>
            <li><button type="button" class="pagination__arr pag_next">&emsp;&rsaquo;</button></li>
        </ul>
        <?php
    } else {
        ?>
            <h2 class="title">Ничего не найдено</h2>
        <?php            
     } 
    wp_reset_postdata();

}
add_action('wp_ajax_filter-data', 'change_cart_filter_data');
add_action('wp_ajax_nopriv_filter-data', 'change_cart_filter_data');




add_action( 'pre_get_posts',  'set_posts_per_page'  );
function set_posts_per_page( $query ) {

  global $wp_the_query;

  if ( ( ! is_admin() ) && ( $query === $wp_the_query ) && ( $query->is_search() ) ) {
    $query->set( 'posts_per_page', 2 );
  }
  elseif ( ( ! is_admin() ) && ( $query === $wp_the_query ) && ( $query->is_archive() ) ) {
    $query->set( 'posts_per_page', 2 );
  }
  return $query;
}

add_filter( 'comment_form_default_fields', 'mo_comment_fields_custom_html' );
function mo_comment_fields_custom_html( $fields ) {
    unset( $fields['comment'] );
    unset( $fields['author'] );
    unset( $fields['email'] );
    unset( $fields['submit'] );
    $fields = [
        'author' => '<div class="revew__input-wrap">' . '<label class="form__label" for="author">Введите ваше имя*' .
            '<input class="form__input" id="author" name="author" type="text" placeholder="Иван Иванов" value="'. esc_attr( $commenter['comment_author'] ) .'" /></label>',
        'email'  => '<label class="form__label" for="email">Ваша почта* ' .
            '<input class="form__input" id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" /></label></div>',
        'city'   =>  '<div class="revew__input-wrap"><label class="form__label" for="city">Город*<input type="text" name="city" class="form__input" placeholder="Димитровград" value="'. __( 'City' ) .'"/></label>',
        'rating' =>  '<div class="revew__rate comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
                        <option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
                        <option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
                        <option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
                        <option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
                        <option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
                        <option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
                    </select></div></div>',
        'comment_field' =>  '<label class="form__label" for="comment">Отзыв:<textarea id="comment" name="comment" class="form__input revew__textarea" placeholder="В произвольной форме"></textarea></label>',
        'submit' =>  '<div class="form__button-wrap"><button class="button_orange form__button" type="submit" name="submit" id="submit">Отправить отзыв</button><p class="form__aftertext">Нажимая на кнопку, вы принимаете <br><a href="'.  get_page_link( 3 ) . '">Политику конфиденциальности</a></p></div>'
    ];
    return $fields;
}
add_filter( 'comment_form_defaults', 'mo_remove_default_comment_field', 10, 1 ); 
function mo_remove_default_comment_field( $defaults ) { if ( isset( $defaults[ 'comment_field' ] ) ) { $defaults[ 'comment_field' ] = ''; } return $defaults; }




add_action( 'wp_ajax_feedback_action', 'ajax_action_callback' );
add_action( 'wp_ajax_nopriv_feedback_action', 'ajax_action_callback' );

function ajax_action_callback() {
	$err_message = array();

    $client_name = $_POST["name"];
    $client_tel = $_POST["tel"];
    $client_mail = $_POST["mail"];
    $client_items = $_POST["items"];

    
    $subject = 'Формаз заказа с сайта Комплект Плюс';
    $email_to = 'edvardi72@gmail.com';

    if ( ! $email_to ) {
        $email_to = get_option( 'admin_email' );
    }

    $body    = "Имя: $client_name \nEmail: $client_mail \nТелефон: $client_tel \n\nТовары: $client_items";
    $headers = 'From: ' . $client_name . ' <' . $email_to . '>' . "\r\n" . 'Reply-To: ' . $email_to;

    if (isset($_POST["name"]) && isset($_POST["tel"])) {
        wp_mail( $email_to, $subject, $body, $headers );
    }

    $message_success = 'Собщение отправлено. С вами свяжутся в ближайшее время.';
    wp_send_json_success( $message_success );

	wp_die();

}


function true_load_posts(){
	$data = unserialize(stripslashes($_POST['query']));
    if ($data['name'] == 'stati') {
        $args['category_name']='articles';
    }
    else if ($data['name'] == 'otzyvy') {
        $args['category_name']='feedback';
    }
    $indexPage = $_POST['index'];
    $args['posts_per_page']=10;
    $args['offset']=10 * $indexPage;
	$q = new WP_Query($args);
    $index = 0;
	if( $q->have_posts() ):
        if($args['category_name'] == 'articles'): ?>
        <div class="articles parent">
        <?php     
            while($q->have_posts()): $q->the_post();
                $index = $index + 1;
                if($index > 10){
                    $index = 1;
                }
                ?>
                <div class="div div<?php echo $index ?>"> 
                    <a class="articles__item" href="<?php echo get_permalink( ); ?>">
                        <img 
                            src="<?php the_post_thumbnail_url(  ); ?>" 
                            class="articles__image" 
                            alt="<?php the_title(); ?>"
                            title="<?php the_title(); ?>">
                        <div class="articles__content">
                            <h3 class="text__link"><?php the_title(); ?></h3>
                            <p><?php the_excerpt(); ?></p>
                        </div>
                        </a>
                </div>
                <?php
            endwhile; ?>
            </div>
        <?php
        elseif ($args['category_name'] == 'feedback'): ?>
        <ul class="feedback__list">
            <?php while($q->have_posts()): $q->the_post();?>
        
            <li class="feedback__item">
                <div class="feedback__head">
                    <?php 
                        $image = get_field('feedback_img');
                            if(!empty($image)) {
                    ?>
                        <img 
                            src="<?php echo $image['url'] ?>" 
                            alt="<?php echo $image['alt'] ?>" 
                            title="<?php echo $image['alt'] ?>"
                            class="feedback__head-img"
                        >
                    <?php
                            } 
                        ?>
                            <div class="feedback__info">
                                    <p class="feedback__accent"><?php the_title(); ?></p>
                                    <a href="<?php the_field('feedback_link'); ?>"><img class="feedback__social-img" src="<?php the_field('feedback_social'); ?>"></a>
                                </div>
                            </div>
                            <p class="feedback__accent"><?php the_field('feedback_title'); ?></p>
                            <p class="feedback__feedback"><?php the_field('feedback_preview');?> <?php the_field('feedback_text'); ?></p>
            </li>
        <?php
            endwhile; ?>
        </ul>
    <?php endif;
        endif;
	wp_reset_postdata();
	die();
}

add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');



 add_action( 'wp_footer', 'mycustom_wp_footer' );
function mycustom_wp_footer() {
    ?>
     <script type="text/javascript">
         document.addEventListener( 'wpcf7mailsent', function( event ) {
         if ( '261' == event.detail.contactFormId || '260' == event.detail.contactFormId) { 
         jQuery('#call-form-popup').removeClass('closed');
         document.querySelector('.wpcf7 form .wpcf7-response-output').style.display = "none"
         document.querySelector('.wpcf7 form .wpcf7-response-output').style.border = "none";
         document.querySelector('.wpcf7 form .wpcf7-response-output').style.padding = "0";
         document.querySelector('.wpcf7 form .wpcf7-response-output').style.margin = "0";
       } else if ('262' == event.detail.contactFormId) {
        jQuery('#price-form-popup').removeClass('closed');
       } else if ('263' == event.detail.contactFormId) {
        jQuery('#ask-form-popup').removeClass('closed');
       }
        }, false );
         </script>
    <?php  
}


function filter_comments_per_page( $comments_per_page, $comment_status ) { 
    return $comments_per_page; 
}; 
         
add_filter( 'comments_per_page', 'filter_comments_per_page', 3, 2 );

function comment_func($comment) {
    $GLOBALS['comment'] = $comment; 
        // print_r($comment);
    ?>
        <li class="card__revew-item" id="review-<?php comment_ID(); ?>">
            <div class="card__revew-info">
                <p class="card__revew-name"><?php echo get_comment_author( $comment->comment_ID ); ?></p>
                <p class="card__revew-date"><?php echo get_comment_meta ( $comment->comment_ID, 'city', true );?></p>
                <p class="card__revew-date"><?php echo get_comment_date(); ?></p>
            </div>
            <div class="card__revew-descr">
                <div class="card__rating-wrapper">
                    <?php
                    $rating_str = get_comment_meta ( $comment->comment_ID, 'rating', true );
                    $rating = (int)$rating_str;
                        for($i = 1; $i <= $rating; $i++) {
                            ?>
                            <div class="card__revew-stars"></div>
                            <?php
                        }
                    ?>
                </div>
                <p class="card__revew-text"> <?php comment_text($comment->comment_ID); ?></p>
            </div>
            
<?php }

function reviews_more() {

    $p_id = $_POST['product_id'];
    $comment_query = $_POST['query'];
    $pages = $_POST['page'];
    $page = (int)$pages;
    
	global $post;
	$post = get_post( $p_id );
	setup_postdata( $post );
 
	wp_list_comments( array(
		'page' => $page, 
        'per_page' => get_option('comments_per_page'),
        'callback' => 'comment_func',
		'style' => 'ol', 
        'short_ping' => true,
        'per_page' => 3,
        'offset' => $page * 3
	) );
}    



add_action('wp_ajax_reviews-action', 'reviews_more');
add_action('wp_ajax_nopriv_reviews-action', 'reviews_more');


?>