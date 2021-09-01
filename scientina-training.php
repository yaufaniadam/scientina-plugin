<?php
/*
   Plugin Name: Scientina Training
   Version: 1.0.0
   Author: Yaufani Adam
   Author URI: https://solusdesain.net
   Description: Plugin
   Text Domain: scientina
   License: GPLv3
*/

defined('ABSPATH') or die('No direct access!');

require_once('inc/cpt.php');
require_once('inc/shortcode.php');
require_once('inc/coupon.php');
require_once('inc/midtrans-php/Midtrans.php');

// require 'update-checker/plugin-update-checker.php';
// $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
//     'https://solusidesain-update-theme.netlify.app/scientina/theme.json',
//     __FILE__, //Full path to the main plugin file or functions.php.
//     'scientina'
// );

// $likes = get_option( 'jsforwp_likes' );
// if ( null == $likes  ) {
//   add_option( 'jsforwp_likes', 0 );
//   $likes = 0;
// }

function jsforwp_frontend_scripts()
{

  wp_enqueue_script(
    'scajax-js',
    plugins_url('/assets/js/scajax.js', __FILE__),
    ['jquery'],
    time(),
    true
  );

  wp_localize_script(
    'scajax-js',
    'scajax_globals',
    [
      'ajax_url'    => admin_url('admin-ajax.php'),
      'nonce'       => wp_create_nonce('scajax_nonce')
    ]
  );
}
add_action('wp_enqueue_scripts', 'jsforwp_frontend_scripts');
require_once('assets/lib/plugin-page.php');


// training_running
add_action('elementor/query/training_running', function ($query) {
  // // Append our meta query
  $meta_query[] = [
    'key' => 'running',
    'value' => '"yes"',
    'compare' => 'like'
  ];
  $query->set('meta_query', $meta_query);
  $query->set('post_type', ['training']);
  $query->set('posts_per_page', 2);
});

// training_scheduled
add_action('elementor/query/training_scheduled', function ($query) {
  // // Append our meta query
  $meta_query[] = [
    'key' => 'running',
    'value' => '"yes"',
    'compare' => 'not like'
  ];
  $query->set('meta_query', $meta_query);
  $query->set('post_type', ['training']);
  $query->set('posts_per_page', 2);
});

add_action('wp_ajax_registrasi', 'registrasi');
add_action('wp_ajax_nopriv_registrasi', 'registrasi');
function registrasi()
{
  // Change the parameter of check_ajax_referer() to 'jsforwp_likes_nonce'
  check_ajax_referer('scajax_nonce');

  $isi = $_POST['isi'];
  $newarray = explode("&", $isi);

  foreach ($newarray as $key => $val) {
    $id_field = explode("=", $val);
    $field[$id_field[0]] = $id_field[1];
  }

  if ($field['submitted'] == 'daftar') {

    $email = urldecode($field['email']);
    $username = str_replace('@', '-', $email);
    $password = $field['password'];
    $telp = $field['telp'];

   
    if (($email) != '') {
      if (!is_email($email)) {
        //invalid email
        sctn_errors()->add('email_invalid', __('Email tidak valid'));
      } else {
        if (email_exists($email)) {
          //Email address already registered
          sctn_errors()->add('email_used', __('Email sudah terdaftar'));
        }
      }
    } else {
      sctn_errors()->add('email_empty', __('Email harus diisi'));
    }

    if (($password) != '') {
      if (strlen($password) < 8) {
        sctn_errors()->add('password_thooshort', __('Password minimal 8 karakter'));
      }
    } else {
      sctn_errors()->add('password_empty', __('Password harus diisi'));
    }

    if (strlen($telp) < 11) {
      sctn_errors()->add('telp_thooshort', __('Telepon minimal 10 angka'));
    } else {
      if (!is_numeric($telp)) {
        sctn_errors()->add('telp_notnumeric', __('Telepon harus berupa angka'));
      }
    }

    $errors = sctn_errors()->get_error_codes();
    $error_msgs = sctn_errors()->get_error_messages();

    $error_combine = array_combine($errors, $error_msgs);

    if (empty($errors)) {
      $userdata = array(
        'user_login'        => sanitize_text_field($username),
        'user_email'        => sanitize_text_field($email),
        'user_pass'         => $password,
        'user_registered'   => date('Y-m-d H:i:s'),
        'role'              => 'subscriber'
      );

      $user_id = wp_insert_user($userdata);

      if ($user_id) {
        // add user meta telp
        add_user_meta($user_id, 'telp', sanitize_text_field($field['telp']));

        // send an email to the admin
        wp_new_user_notification($user_id);

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);      
      }       
      $error = 'noerror';      
        
    } else {
      $error = 'error';
      $response['error_code'] = $error_combine;
    }

  }  else {


    $response['button'] = $field['submitted'];
    $error = 'noerror';


  }

  if($error != 'error') {
    $wpdb = $GLOBALS['wpdb'];
        $query = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID =" . $field['post_id'], ARRAY_A);

        $itemArray = array(
          $query['ID'] => array(
            "judul" => $query["post_title"],
            "harga" => sanitize_text_field($field['harga']),
            "jml_peserta" => sanitize_text_field($field['jml_peserta']),
          )    
        );       

        $data_order = array(
          'post_title'    =>  $query["post_title"],
          'post_status'   => 'pending',
          'post_author'   => get_current_user_id(),
          'post_type'   => 'orders',
        );

        // Insert the post into the database.
        $result = wp_insert_post($data_order);
/*
        if($result) {

          $total_harga = $field["harga"]*$field["jml_peserta"];

          add_post_meta($result, 'jml_peserta', $field['jml_peserta'], true);
          add_post_meta($result, 'total_harga', $total_harga, true);
          add_post_meta($result, 'training', $query['ID'], true);

          $nonce = wp_create_nonce( 'scajax_nonce' );
          
          $html = '';
          $html .= '<h5>Pesanan Anda</h5>';
          $html .= "<p>Masukkan Nama Peserta</p>";
          $html .=  '<form action="" method="POST" class="form_checkout">';
          $html .=  '<input type="hidden" name="nonce" value="'. $nonce .'">';
          $html .=  '<input type="hidden" name="total_harga" id="total_harga" value="'. $total_harga .'">';
          $html .=  '<input type="hidden" name="jml_peserta" id="jml_peserta" value="'. $field["jml_peserta"] .'">';
          for ($i = 1; $i <= $field["jml_peserta"]; $i++) {
            $html .= '<div class="mb-3 row">
                  <label for="nama peserta" class="col-sm-4 col-form-label">Nama Peserta*</label>
                  <div class="col-sm-8">
                    <input type="text" name="peserta'.$i.'" class="form-control"  value="" required>
                    <span class="invalid-feedback error peserta' . $i. '_empty d-block"></span>
                  </div>
                </div>';
          }
          $html .='<div class="mb-3 row">
              <label for="nama peserta" class="col-sm-4 col-form-label">Punya kupon?</label>
              <div class="col-sm-8">    
                <div class="input-group mb-3">              
                  <input type="text" class="form-control" name="coupon" id="kode_kupon" style="width:100px !important;">
                  <span class="btn btn-warning" type="button" id="cek_kupon">
                  Cek Kupon</span>
                  <span class="invalid-feedback message_kupon d-block" ></span>
                </div>
              </div>
            </div>';

          $html .= "<p><strong>" . $query["post_title"] . "</strong><br>             
              Rp</span> " . number_format($field["harga"]) . " x " . $field["jml_peserta"] . " = Rp <span class='harga_asli'><span class='tru'></span>" . number_format($total_harga) . "</span> &nbsp;<span class='harga_baru'></span>                      
            </p>";
          $html .= "<input type='hidden' id='training_id' name='training_id' value='". $query["ID"] ."'>";
          $html .= "<input type='hidden' id='training_title' name='training_title' value='". $query["post_title"] ."'>";
          $html .= "<input type='hidden' id='harga_diskon' name='harga_diskon' value=''>";
          $html .= "<input type='hidden' name='submitted' value='checkout'>
              <hr style='border-top:1px solid white; padding:10px 0;'>
              <p><button type='submit' class='button button_beli btn btn-warning' id='button_checkout'>
                  <span class='spinner-border text-light spinner-border-sm d-none' role='status' aria-hidden='true'></span>
                      <span class='visually-hidden'>Loading...</span>
              Konfirmasi Pesanan</button></p>";  
          $html .= "</form>"; 
          $response['html'] = $html;

        }
*/
        
  }
  
  $response['error'] = $error;
  // $response['jml_peserta'] = $field['jml_peserta'];
  // $response['isi'] = $field['submitted'];
  $response['type'] = 'success';
  $response['redir'] = urldecode($field['url']);
  $response = json_encode($response);
  echo $response;
  die();
}

// used for tracking error messages on registration
function sctn_errors()
{
  static $wp_error; // global variable handle
  return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

//////////////////////
///    CHECKOUT   ////
//////////////////////

add_action('wp_ajax_checkout', 'checkout');
add_action('wp_ajax_nopriv_checkout', 'checkout');
function checkout()
{
  // Change the parameter of check_ajax_referer() to 'jsforwp_likes_nonce'
  check_ajax_referer('scajax_nonce');

  $isi = $_POST['isi'];

  $newarray = explode("&", $isi);

  foreach ($newarray as $key => $val) {
    $id_field = explode("=", $val);
    $field[urldecode($id_field[0])] = urldecode($id_field[1]);
  }

  if ($field['submitted'] == 'checkout') {

    for ($i = 1; $i <= $field['jml_peserta']; $i++) {

      ${'peserta' . $i} = $field['peserta' . $i];

      if ((${'peserta' . $i}) == '') {
        sctn_errors()->add('peserta' . $i . '_empty', __("Nama peserta $i harus diisi"));
      }
    }

    $errors = sctn_errors()->get_error_codes();
    $error_msgs = sctn_errors()->get_error_messages();

    $errors = array_combine($errors, $error_msgs);

    if (empty($errors)) {
      $users = array();
      for ($i = 1; $i <= $field['jml_peserta']; $i++) {
        $data_peserta = array(
            'post_title'    => $field['peserta' . $i],
            'post_status'   => 'pending',
            'post_author'   => get_current_user_id(),
            'post_type'   => 'participant',
        );

        // Insert the post into the database.
        $users[] .= wp_insert_post($data_peserta);
      }

      $data_order = array(
        'post_title'    =>  $field['training_title'],
        'post_status'   => 'pending',
        'post_author'   => get_current_user_id(),
        'post_type'   => 'orders',
      );

      // Insert the post into the database.
      $result = wp_insert_post($data_order);

      if ($result && !is_wp_error($result)) {
        $post_id = $result;
        add_post_meta($post_id, 'training', $field['training_id'], true);
        add_post_meta($post_id, 'participant', $users, true);
        add_post_meta($post_id, 'total_harga', $field['total_harga'], true);
        add_post_meta($post_id, 'harga_diskon', $field['harga_diskon'], true);
        add_post_meta($post_id, 'jml_peserta', $field['jml_peserta'], true);
        add_post_meta($post_id, 'kode_kupon', $field['coupon'], true);
      }
      $error = 'noerror';
    } else {
      $error = 'error';
      $response['error_code'] = $errors;
    }/* empty error */
  }

  $response['redir'] = get_bloginfo('url') ."/proses-bayar";
  $response['error'] = $error;
  $response['type'] = 'success';
  $response = json_encode($response);
  echo $response;
  die();
}

add_shortcode('proses_bayar', 'proses_bayar');
function proses_bayar()
{
    $user = wp_get_current_user();

    // if (isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

    //     if ($_POST['submitted'] == 'bayar') {            

            \Midtrans\Config::$serverKey = 'SB-Mid-server-nfg_ilmmRSPvnW3RSa_DymDW';
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $jml_peserta = get_post_meta($result, 'jml_peserta', true);
            $harga = get_post_meta($result, 'harga', true);

            $transaction_details = array(
                'order_id' => rand(),
                'gross_amount' => $harga,
            );

            $item_details = array(
                'id' => $post_id,
                'price' => $harga,
                'quantity' => $jml_peserta,
                'name' => $order["post_title"]
            );

            $item_details = array($item_details);

            $customer_details = array(
                'first_name' => $user->display_name,
                'last_name' => '',
                'email' => $user->user_email,
                'phone' => '08562563456',
                'billing_address' => '',
                'shipping_address' => ''
            );

            $enable_payments = array('mandiri_clickpay', 'credit_card');

            $transaction = array(
                'enabled_payments' => $enable_payments,
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details,
                'item_details' => $item_details
            );

            $snapToken = \Midtrans\Snap::getSnapToken($transaction);

            $cart = '';

            $cart .= '<h5><strong>Pesanan Anda</strong></h5>';

            $cart .= "<table class='cart'><thead><tr><th>Training</th><th>Nama Peserta</th><th>Total</th><th>&nbsp;</th><tr></thead>";
            $cart .= "<tr>       
                <td>" . $order["post_title"] . "</td>
                <td><ol>" . $pst . "</ol></td>
                <td style='text-align:right;'><span style='float:left;'>Rp</span> " . number_format(get_post_meta($result, 'total_harga', true), 2) . " </td>     
                <td style='text-align:center;'><button id='pay-button' style='background:#f4511e; border-radius:5px;'>Bayar</button></td>      
                <tr>
            ";

            $cart .= "</table>";

            $cart .= '
            <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-NUHDTW6uipcvE7sz"> </script>
            <script
            src="https://code.jquery.com/jquery-3.6.0.slim.min.js"
            integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI="
            crossorigin="anonymous"></script>
            <script type="text/javascript"> 
                $("#pay-button").on("click", function() {                 
                    snap.pay("' . $snapToken . '", {
                        onSuccess: function(result){
                        document.getElementById("result-json").innerHTML += JSON.stringify(result, null, 2)
                        },
                        onPending: function(result) {
                        document.getElementById("result-json").innerHTML += JSON.stringify(result, null, 2)
                        },
                        onError: function(result) {
                        document.getElementById("result-json").innerHTML += JSON.stringify(result, null, 2)
                        }
                    });
                });            
            </script>';

            echo $cart;
    //     } 
    // }
}

function panggil_midtrans() {

  echo "midtrans di sini";

  
}


add_action('wp_ajax_test', 'test');
add_action('wp_ajax_nopriv_test', 'test');
function test()
{
  
  check_ajax_referer('scajax_nonce');

  echo "<h3>Isi data Pengguna</h3>";
  
}

