<!DOCTYPE html>
<html style="font-family: 'Roboto Slab', serif; scroll-behavior: smooth;" lang="es">
<head>
<!-- Este sitio está Ultilizando Lebianch SEO theme https://lebianch.com/ -->
	

    <meta charset="UTF-8">
    <meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
    <meta name="theme-color" content="#ffffff">

    <?php wp_head(); ?>

<body id="page-top">

    <nav <?php if ( is_user_logged_in() ) { echo 'style="top:32px"';} ?> class="navbar navbar-light navbar-expand-lg fixed-top bg-white" id="mainNav">
        <div class="container">
		<?php 
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );

      if ( has_custom_logo() ) {
          echo '<a style="max-width:181px" href="'.site_url().'" aria-label="'.get_bloginfo( 'name' ).'" >';
              echo '<img style="max-height:75px; padding:10px 0; width:100%" load="lazy" class="img-fluid" src="' . esc_url( $logo['0'] ) . '" aria-label="'. get_bloginfo( 'name' )  .'" alt="' . get_bloginfo( 'description' ) . '">';

      } else {
        echo '<a href="'.site_url().'" aria-label="'.get_bloginfo( 'name' ).'" >';
        echo '<img style="border-radius:10px" load="lazy" class="img-fluid" src="' .get_theme_file_uri() . '/assets/img/logo-250x100.jpg" aria-label="'. get_bloginfo( 'name' )  .'" alt="' . get_bloginfo( 'description' ) . '">';

      }
    ?>
		    </a>
		
        <?php 
    wp_nav_menu( array(
      'theme_location'  => 'primary',
      'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
      'container'       => 'div',
      'container_class' => 'collapse navbar-collapse',
      'container_id'    => 'bs-example-navbar-collapse-1',
      'menu_class'      => 'navbar-nav mr-auto',
      'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
      'walker'          => new WP_Bootstrap_Navwalker(),
    ) );
    ?>
            
        </div>
		
	

	

	
  

  
    </nav>