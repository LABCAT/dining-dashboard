<?php
/**
  * Registers post type for food and drink menus
 *
 * @class     MenuPostType
 * @Version: 0.0.1
 * @package   DiningDashboard/PostTypes
 * @category  Class
 * @author    MySite Digital
 */
namespace MySiteDigital\DiningDashboard;


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * MenuPostType Class.
 */
class MenuPostType {

    use PostType;

    public static $post_type_slug = 'menu';

    public static $post_type_slug_plural = 'menus';

    public static $archive_template = 'menus-archive.php';

    public static $single_template = 'menu-single.php';
    
    public function __construct()
    {
        add_action( 'init', [ $this , 'init' ] );
        add_action( 'init', [ $this , 'register_settings' ], 4 );
        add_action( 'init', [ $this , 'register_post_type' ], 5 );
        add_action( 'transition_post_status', [ $this , 'update_archive_slug' ], 10, 3 );
        

        //add_filter( 'gettext',  [ $this, 'custom_translations' ] , 999 );
        //add_filter( 'the_title', array( $this, 'custom_translations' ), 10, 2 );
    }

    public function register_settings() {
        register_setting(
            'menu_post_type_archive_slug',
            'menu_post_type_archive_slug',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'show_in_rest' => true,
                'default' => self::$post_type_slug
            )
        );
    }

    public function register_post_type() {
        if ( post_type_exists( self::$post_type_slug ) ) {
            return;
        }

        $archive_slug = get_option( 'menu_post_type_archive_slug' );
        $menu_name = ucfirst( ( self::count_posts() > 1 ) ? self::$post_type_slug_plural : self::$post_type_slug );
        
        $labels = [
            'name'                  => __( $menu_name, self::$post_type_slug ),
            'singular_name'         => _x(
                                            $menu_name,
                                            'Menus post type singular name',
                                            self::$post_type_slug
                                        ),
            'add_new'               => __( 'Add Menu', self::$post_type_slug ),
            'add_new_item'          => __( 'Add New Menu', self::$post_type_slug ),
            'edit'                  => __( 'Edit', self::$post_type_slug ),
            'edit_item'             => __( 'Edit Menu', self::$post_type_slug ),
            'new_item'              => __( 'New Menu', self::$post_type_slug ),
            'view'                  => __( 'View Menu', self::$post_type_slug ),
            'view_item'             => __( 'View Menu', self::$post_type_slug ),
            'search_items'          => __( 'Search Menu', self::$post_type_slug ),
            'not_found'             => __( 'No '.$menu_name.' found', self::$post_type_slug ),
            'not_found_in_trash'    => __( 'No '.$menu_name.' found in trash', self::$post_type_slug ),
            'parent'                => __( 'Parent Menu', self::$post_type_slug ),
            'menu_name'             => _x( $menu_name, 'Admin menu name', self::$post_type_slug ),
            'filter_items_list'     => __( 'Filter Menus', self::$post_type_slug ),
            'items_list_navigation' => __( 'Menus navigation', self::$post_type_slug ),
            'items_list'            => __( 'Menus list', self::$post_type_slug ),
        ];

        $args =  [
            'labels'              => $labels,
            'description'         => __( 'This is where Menus are stored.', self::$post_type_slug ),
            'capability_type'     => 'page',
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => 'index.php',
            'supports'            => [ 
                    'editor', 
                    'title', 
                    'author',
                    'page-attributes',
            ],
            'show_in_rest'        => true,
            'has_archive'         => $archive_slug,
            'rewrite'             => [
                                        'slug' => 'menu',
                                        'with_front' => false
                                    ]
        ];

        register_post_type(
            self::$post_type_slug,
            $args
        );

        $flush_rewrite = get_option( 'menu_post_type_flush_rewrite_rules' );

        if ( $flush_rewrite ) {
            flush_rewrite_rules( true );
            update_option( 'menu_post_type_flush_rewrite_rules', false, true );
        }
    }

    public function update_archive_slug( $new_status, $old_status, $post ) {

        if( $post->post_type == self::$post_type_slug ){
            $current_slug = get_option( 'menu_post_type_archive_slug' );
            $new_slug = ( self::count_posts() > 1 ) ? self::$post_type_slug_plural : self::$post_type_slug;
            if( $new_slug !== $current_slug ){
                update_option( 'menu_post_type_archive_slug', $new_slug, false );
                update_option( 'menu_post_type_flush_rewrite_rules', true, false );
            }
        }
    }    

    public function custom_translations( $translated ) {

        //var_dump($translated);
        switch ( $translated ) {
			case 'Add title' :
                $translated = 'Add menu title';
                break;
            case 'Write heading…';
                $translated = 'Section title (eg Entree, Mains, Desert)';
                break;
        }

        return $translated;
    }
}

new MenuPostType();