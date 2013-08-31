<?php
/*
	Custom functions designed specifically for Akvo Responsive theme.
	Feel free to add your own dynamic functions, or clear out this file entirely.
*/
add_theme_support( 'post-thumbnails' );
register_nav_menus(array(
    'header-menu' => 'Header Menu'
));

/**
 * Setting up custom sidebars
 *
 */
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Main Sidebar',
        'id' => 'main',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="wtitle">',
        'after_title' => '</h3>'
    ));
    
    register_sidebar(array(
        'name' => 'Responsive Sidebar',
        'id' => 'responsive',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="wtitle">',
        'after_title' => '</h3>'
    ));
}
function new_excerpt_more($more)
{
    global $post;
    return '... <a href="' . get_permalink($post->ID) . '" class="moreLink">Read more</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');
function the_breadcrumb()
{
    
    $sep = '   &rsaquo;  ';
    
    if (!is_front_page()) {
        
        echo '<div class="breadcrumbs wrapper">';
        echo '<a href="';
        echo get_option('home');
        echo '">';
        bloginfo('name');
        echo '</a>' . $sep;
        
        if (is_category() || is_single()) {
            the_category(', ');
        } elseif (is_archive() || is_single()) {
            if (is_day()) {
                printf(__('%s', 'text_domain'), get_the_date());
            } elseif (is_month()) {
                printf(__('%s', 'text_domain'), get_the_date(_x('F Y', 'monthly archives date format', 'text_domain')));
            } elseif (is_year()) {
                printf(__('%s', 'text_domain'), get_the_date(_x('Y', 'yearly archives date format', 'text_domain')));
            } else {
                _e('Blog Archives', 'text_domain');
            }
        }
        
        if (is_single()) {
            echo $sep;
            the_title();
        }
        
        if (is_page()) {
            echo the_title();
        }
        
        if (is_home()) {
            global $post;
            $page_for_posts_id = get_option('page_for_posts');
            if ($page_for_posts_id) {
                $post = get_page($page_for_posts_id);
                setup_postdata($post);
                the_title();
                rewind_posts();
            }
        }
        
        echo '</div>';
    }
}
/**
 * Customize the 'Read More' link text
 *
 */
function custom_more_link($link, $link_text)
{
    return str_replace($link_text, 'Read More &raquo;', $link);
}
add_filter('the_content_more_link', 'custom_more_link', 10, 2);


/**
 * Remove the hash(#) from all 'Read More' links
 *
 */
function remove_more_jump($link)
{
    $offset = strpos($link, '#more-');
    if ($offset) {
        $end = strpos($link, '"', $offset);
    }
    if ($end) {
        $link = substr_replace($link, '', $offset, $end - $offset);
    }
    return $link;
}
add_filter('the_content_more_link', 'remove_more_jump');


//ADD PAGES NAME AS A BODY CLASS

function my_bodyclass() // add pagename as class to <body> tag
{
    global $wp_query;
    $page = '';
    if (is_front_page()) {
        $page = 'home';
    } elseif (is_page()) {
        $page = $wp_query->query_vars["pagename"] . ' Page';
    }
    if ($page) {
        echo ' class= "' . $page, '"';
    }
    if ($page = 'blog') {
        echo ' class= "' . $page, ' ' . '"';
    }
}



//LIMITS NUMBER OF WORDS WHEN USING THE EXcerpt FUNCTION

function custom_excerpt_length( $length ) {
	return 10;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

?>