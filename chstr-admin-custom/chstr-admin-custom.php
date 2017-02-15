<?php
/*
Plugin Name: Personnalisation du BackOffice 
Version: 1
Author: chstr
*/

class Chstr_Custom_Admin
{
    public function __construct()
    {
		if ( is_admin() ) 
		{
			$this->editAdminBar() ; // Barre d'administration
			$this->editDashboard() ; // Dashboard
		}
		$this->editPageLogin() ; // Page de connexion
    }
	
	public function editPageLogin()
	{
		/*
		Modifie le logo
		*/
		function chstr_login_head() {
			 echo '<style type="text/css">
			 h1 a { background-image:url('. get_stylesheet_directory_uri() .'images/logo.jpg) !important; margin-bottom: 10px; padding: 80px;}
			 </style>';
		}
		add_action( 'login_head', 'chstr_login_head');
		
		/*
		Modifie le titre (alt)
		*/
		function chstr_login_headertitle(){
			return get_bloginfo('name');
		}
		add_filter('login_headertitle', 'chstr_login_headertitle');
		
		/*
		Coche par défaut "Se souvenir de moi"
		*/
		function chstr_check_rememberme(){
			global $rememberme;
			$rememberme = 1;
		}
		add_action("login_form", 'chstr_check_rememberme');
	}
	
	public function editAdminBar()
	{
		/*
		Supprime certains liens de la barre d'admin
		Liste des liens existants : http://www.naxialis.com/personnalise-l-affichage-des-options-de-la-barre-d-administration-de-wordpress/
		*/
		function chstr_admin_bar() {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu('wp-logo');
			$wp_admin_bar->remove_menu('updates');
			$wp_admin_bar->remove_menu('comments');
			$wp_admin_bar->remove_menu('new-content');
		}
		add_action( 'wp_before_admin_bar_render', 'chstr_admin_bar' );
		
		/*
		Personnalise l'affichage de "Mon compte" en haut à droite
		© http://www.geekpress.fr/modifier-howdy-admin-bar/
		*/
		function good_bye_howdy( $wp_admin_bar ) {
			global $current_user;
			$my_account=$wp_admin_bar->get_node('my-account');
			$howdy = sprintf( __('Salutations, %1$s'), $current_user->display_name );
			if( in_array( $current_user->user_login, get_super_admins() ) ) :
				$my_role = __( 'Super-admin' );
			else : 
				$my_role = translate_user_role( $GLOBALS['wp_roles']->role_names[$current_user->roles[0]] );
			endif;
			$title = str_replace( $howdy, sprintf( '%1$s - %3$s (%2$s)', $current_user->display_name, $my_role, $current_user->user_email ), $my_account->title );
			$wp_admin_bar->add_node( array(
				'id' => 'my-account',
				'title' => $title,
				'meta' => $my_account->meta
			) );
		}
		add_filter( 'admin_bar_menu', 'good_bye_howdy' );
		
	}
	
	public function editDashboard()
	{
		/*
		Suppime le bloc "Bienvenue"
		*/
		remove_action('welcome_panel', 'wp_welcome_panel');

		/*
		Supprime certains blocs 
		*/
		function chstr_remove_dashboard_widgets () {
			remove_meta_box( 'dashboard_quick_press',   'dashboard', 'side' );
			remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_primary',       'dashboard', 'side' );
			remove_meta_box( 'dashboard_secondary',     'dashboard', 'side' );
			remove_meta_box( 'dashboard_incoming_links','dashboard', 'normal' );
			remove_meta_box( 'dashboard_plugins',       'dashboard', 'normal' );
		}
		add_action('wp_dashboard_setup', 'chstr_remove_dashboard_widgets');
		
		/*
		Crée un nouveau bloc personnalisé 
		*/
		function chstr_register_widget() {
			wp_add_dashboard_widget(
				'chstr_widget_1',
				'Widget 1',
				'chstr_content_widget_1'
			);
			
			wp_add_dashboard_widget(
				'chstr_widget_2',
				'Widget 2',
				'chstr_content_widget_2'
			);
		}
		function chstr_content_widget_1() {
			echo 'Lorem Lipsum - 1';
		}
		function chstr_content_widget_2() {
			echo 'Lorem Lipsum - 2';
		}
		add_action( 'wp_dashboard_setup', 'chstr_register_widget' );
	}
	
}

new Chstr_Custom_Admin();