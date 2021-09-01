<?php

/* --------------------- TRAINING ------------------------*/

function create_post_types_training()
{
  $label = array(
    'name'         => __('Training', 'scientina'),
    'singular_name'   => __('Training', 'scientina'),
    'add_new'       => _x('Add New', 'Training', 'scientina'),
    'add_new_item'     => __('Add New Training', 'scientina'),
    'edit_item'     => __('Edit Training', 'scientina'),
    'new_item'       => __('New Training', 'scientina'),
    'view_item'     => __('View Training', 'scientina'),
    'search_items'     => __('Search Training', 'scientina'),
    'not_found'     => __('No Training found', 'scientina'),
    'not_found_in_trash' => __('No Training found in Trash', 'scientina'),
    'parent_item_colon' => ''
  );
  $args = array(
    'labels'       => $label,
    'description'     => __('All Training upload here', 'scientina'),
    'public'       => true,
    'supports'      => array('title', 'editor', 'thumbnail', 'custom-fields'),
    'query_var'     => true,
    'rewrite'       => array('slug' => 'training'),
    'menu_icon'      => 'dashicons-awards',
    'show_in_nav_menus' => true,
    'has_archive'     => true,
    'menu_position'   => 20,
    'publicly_queryable'   => true,
    'show_ui'   => true,
    'exclude_from_search'=>false
  );
  register_post_type('training', $args);
}
add_action('init', 'create_post_types_training');

// Add taxonomies
add_action('init', 'create_taxonomies_training');


function create_taxonomies_training()
{
  $training_cats = array(
    'name' => __('Categories', 'scientina'),
    'singular_name' => __('Category', 'scientina'),
    'search_items' =>  __('Search Categories', 'scientina'),
    'all_items' => __('All trainings Categories', 'scientina'),
    'parent_item' => __('Parent Category', 'scientina'),
    'parent_item_colon' => __('Parent Category:', 'scientina'),
    'edit_item' => __('Edit Category', 'scientina'),
    'update_item' => __('Update Category', 'scientina'),
    'add_new_item' => __('Add New Category', 'scientina'),
    'new_item_name' => __('New Category Name', 'scientina'),
    'choose_from_most_used'  => __('Choose from the most used Categories', 'scientina')
  );

  register_taxonomy('training_cats', 'training', array(
    'hierarchical' => true,
    'labels' => $training_cats,
    'query_var' => true,
    'rewrite' => array('slug' => 'training-cats'),
  ));

  $training_area = array(
    'name' => __('Area', 'scientina'),
    'singular_name' => __('Area', 'scientina'),
    'search_items' =>  __('Search Area', 'scientina'),
    'all_items' => __('All trainings Area', 'scientina'),
    'parent_item' => __('Parent Area', 'scientina'),
    'parent_item_colon' => __('Parent Area:', 'scientina'),
    'edit_item' => __('Edit Area', 'scientina'),
    'update_item' => __('Update Area', 'scientina'),
    'add_new_item' => __('Add New Area', 'scientina'),
    'new_item_name' => __('New Area Name', 'scientina'),
    'choose_from_most_used'  => __('Choose from the most used Area', 'scientina')
  );

  register_taxonomy('area', 'training', array(
    'hierarchical' => true,
    'labels' => $training_area,
    'query_var' => true,
    'rewrite' => array('slug' => 'area'),
  ));
}

/* --------------------- ORDER ------------------------*/

function create_post_types_order()
{
  $label = array(
    'name'         => __('Orders', 'scientina'),
    'singular_name'   => __('Order', 'scientina'),
    'add_new'       => _x('Add New', 'Order', 'scientina'),
    'add_new_item'     => __('Add New Order', 'scientina'),
    'edit_item'     => __('Edit Order', 'scientina'),
    'new_item'       => __('New Order', 'scientina'),
    'view_item'     => __('View Order', 'scientina'),
    'search_items'     => __('Search Order', 'scientina'),
    'not_found'     => __('No Order found', 'scientina'),
    'not_found_in_trash' => __('No Order found in Trash', 'scientina'),
    'parent_item_colon' => ''
  );
  $args = array(
    'labels'       => $label,
    'description'     => __('All Order upload here', 'scientina'),
    'public'       => true,
    'supports'      => array('title', 'author', 'custom-fields'),
    'query_var'     => true,
    'rewrite'       => array('slug' => 'orders'),
    'menu_icon'      => 'dashicons-tickets-alt',
    'show_in_nav_menus' => false,
    'has_archive'     => false,
    'menu_position'   => 20,
  );
  register_post_type('orders', $args);
}
add_action('init', 'create_post_types_order');

/* --------------------- Trainer ------------------------*/

function create_post_types_trainer()
{
  $label = array(
    'name'         => __('Trainers', 'scientina'),
    'singular_name'   => __('Trainer', 'scientina'),
    'add_new'       => _x('Add New', 'Trainer', 'scientina'),
    'add_new_item'     => __('Add New Trainer', 'scientina'),
    'edit_item'     => __('Edit Trainer', 'scientina'),
    'new_item'       => __('New Trainer', 'scientina'),
    'view_item'     => __('View Trainer', 'scientina'),
    'search_items'     => __('Search Trainer', 'scientina'),
    'not_found'     => __('No Trainer found', 'scientina'),
    'not_found_in_trash' => __('No Trainer found in Trash', 'scientina'),
    'parent_item_colon' => ''
  );
  $args = array(
    'labels'       => $label,
    'description'     => __('All Trainer upload here', 'scientina'),
    'public'       => true,
    'supports'      => array('title', 'editor', 'thumbnail'),
    'query_var'     => true,
    'rewrite'       => array('slug' => 'trainer'),
    'menu_icon'      => 'dashicons-businessman',
    'show_in_nav_menus' => false,
    'has_archive'     => false,
    'menu_position'   => 20,
  );
  register_post_type('trainer', $args);
}
add_action('init', 'create_post_types_trainer');

/* --------------------- Participant ------------------------*/

function create_post_types_participant()
{
  $label = array(
    'name'         => __('Participants', 'scientina'),
    'singular_name'   => __('Participant', 'scientina'),
    'add_new'       => _x('Add New', 'Participant', 'scientina'),
    'add_new_item'     => __('Add New Participant', 'scientina'),
    'edit_item'     => __('Edit Participant', 'scientina'),
    'new_item'       => __('New Participant', 'scientina'),
    'view_item'     => __('View Participant', 'scientina'),
    'search_items'     => __('Search Participant', 'scientina'),
    'not_found'     => __('No Participant found', 'scientina'),
    'not_found_in_trash' => __('No Participant found in Trash', 'scientina'),
    'parent_item_colon' => ''
  );
  $args = array(
    'labels'       => $label,
    'description'     => __('All Participant upload here', 'scientina'),
    'public'       => true,
    'supports'      => array('title', 'editor', 'thumbnail'),
    'query_var'     => true,
    'rewrite'       => array('slug' => 'participant'),
    'menu_icon'      => 'dashicons-businessman',
    'show_in_nav_menus' => false,
    'has_archive'     => false,
    'menu_position'   => 20,
  );
  register_post_type('participant', $args);
}
add_action('init', 'create_post_types_participant');