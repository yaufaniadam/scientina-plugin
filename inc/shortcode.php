<?php

/* -- SHORTCODE -- */

/*-------- Button Beli ----------*/
add_shortcode('button_beli', 'button_beli');
function button_beli()
{
    $post_id = get_the_ID();

    $status_transaksi = status_transaksi(get_current_user_id(), $post_id);

    $url = '';
    $form = '';  
    $user = wp_get_current_user();   

    if (!is_user_logged_in()) { 

        $form .= '<div id="load"><form action="' . $url . '" method="POST" class="pendaftaran"><input type="text" name="url" id="url" class="" value="'. get_the_permalink() .'">';

        $form .= '<p style="text-align:center;"><a href="' . esc_url(wp_login_url(get_permalink() . '/?training_id='.  get_the_ID())) . '">Login disini </a>jika sudah terdaftar.</p>';

        $form .= "<div id='daftar_error'><span class='alert alert-danger d-block'>Error! Periksa kembali.</span></div>";
        
        $form .= ' 
        <div class="form-group row">
            <label for="email" class="col-sm-4 col-form-label">Email*</label>
            <div class="col-sm-8">
                <input type="text" name="email" id="email" class="form-control" value="">
                <span class="invalid-feedback error email_empty email_invalid email_used"></span>
            </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-sm-4 col-form-label">Password*</label>
            <div class="col-sm-8">
                <input type="password" name="password" id="password" class="form-control" value="">
                <span class="invalid-feedback error password_thooshort password_empty"></span>
            </div>
        </div>
        <div class="form-group row">
            <label for="telp" class="col-sm-4 col-form-label">Telp/WA* </label>
            <div class="col-sm-8">
                <input type="text" name="telp" id="telp" class="form-control" value="" placeholder="Contoh : 085612344567">
                <span class="invalid-feedback error telp_thooshort telp_notnumeric"></span>
            </div>
        </div>';

        $form .= "<input type='hidden' name='submitted' value='daftar'>";     
        
        $form .= '<div class="form-group row">
            <label for="jml_peserta" class="col-sm-4 col-form-label">Jumlah Peserta* </label>
            <div class="col-sm-8">
                <input type="number" name="jml_peserta" id="jml_peserta" class="form-control" value="1" min="1" value="1">
            </div>
        </div>';   
 
        $form .= '<input type="hidden" name="harga" id="harga" value="' . get_field('harga', get_the_ID()) . '" />';
        $form .= '<input type="hidden" name="post_id" id="post_id" value="' . get_the_ID() . '" />'; 
        $form .= '<div class="form-group row mt-3">
                <label for="button" class="col-sm-4 col-form-label">&nbsp;</label>
                <div class="col-sm-8">
                    <button type="submit" class="btn btn-warning btn-md button button_beli" id="daftar">
                        <span class="spinner-border text-light spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Loading...</span>
                        Beli Program                
                    </button> 
                    </div>
                </div>'; 
        $form .= "<p class='tampil-data text-white'>* Wajib diisi</p>
        </form>
        ";

        $form .= "</div>";

    } else {

        // dia login
        // cek status transaksi, jika ada transaksi, maka jalankan transaksi

        if($status_transaksi == 0) {
            $form .= '<div id="load"><form action="' . $url . '" method="POST" class="pendaftaran">';
            $form .= "<input type='hidden' name='submitted' value='add'>"; 

            $form .= '<div class="form-group row">
            <label for="jml_peserta" class="col-sm-4 col-form-label">Jumlah Peserta* </label>
            <div class="col-sm-8">
                <input type="number" name="jml_peserta" id="jml_peserta" class="form-control" value="1" min="1" value="1">
            </div>
        </div>';   
 
            $form .= '<input type="hidden" name="harga" id="harga" value="' . get_field('harga', get_the_ID()) . '" />';
            $form .= '<input type="hidden" name="post_id" id="post_id" value="' . get_the_ID() . '" />'; 
            $form .= '<div class="form-group row mt-3">
                    <label for="button" class="col-sm-4 col-form-label">&nbsp;</label>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-warning btn-md button button_beli" id="daftar">
                            <span class="spinner-border text-light spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="visually-hidden">Loading...</span>
                            Beli Program                
                        </button> 
                        </div>
                    </div>'; 
            $form .= "<p class='tampil-data text-white'>* Wajib diisi</p>
            </form>
            ";
            $form .= "</div>";
        } else {

            $args = array(
                'post_type'     => 'orders',
                'post_status'   => 'pending',
                'author'   => get_current_user_id(),
                'meta_query'    => array (
                    'key' => 'training',
                    'value' => $post_id,
                ),
            );

            $postslist = get_posts( $args );

            foreach ($postslist as $post) {  
                $jml_peserta = get_field('jml_peserta', $post->ID);  
                $total_harga = get_field('total_harga', $post->ID);  
    
              $nonce = wp_create_nonce( 'scajax_nonce' );
            // $total_harga = $field["harga"]*$field["jml_peserta"];
                $html = '';
                $html .= '<div id="load"><h5>Selesaikan Pesanan Anda</h5>';
                $html .= "<p>Masukkan Nama Peserta</p>";
                $html .=  '<form action="" method="POST" class="form_checkout">';
                $html .=  '<input type="hidden" name="nonce" value="'. $nonce .'">';
                $html .=  '<input type="hidden" name="total_harga" id="total_harga" value="'. $total_harga .'">';
                $html .=  '<input type="hidden" name="jml_peserta" id="jml_peserta" value="'. $jml_peserta .'">';
                for ($i = 1; $i <= $jml_peserta; $i++) {
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
        
                $html .= "<p><strong>" . $post->post_title . "</strong><br>             
                    Rp</span> " . number_format(get_field('harga', $post->ID)) . " x " . $jml_peserta . " = Rp <span class='harga_asli'><span class='tru'></span>" . number_format($total_harga) . "</span> &nbsp;<span class='harga_baru'></span>                      
                </p>";
                $html .= "<input type='hidden' id='training_id' name='training_id' value='". $post->ID ."'>";
                $html .= "<input type='hidden' id='training_title' name='training_title' value='". $post->post_title ."'>";
                $html .= "<input type='hidden' id='harga_diskon' name='harga_diskon' value=''>";
                $html .= "<input type='hidden' name='submitted' value='checkout'>
                    <hr style='border-top:1px solid white; padding:10px 0;'>
                    <p><button type='submit' class='button button_beli btn btn-warning' id='button_checkout'>
                        <span class='spinner-border text-light spinner-border-sm d-none' role='status' aria-hidden='true'></span>
                            <span class='visually-hidden'>Loading...</span>
                    Konfirmasi Pesanan</button></p>";  
                $html .= "</form></div>"; 
        
                    echo $html;
            }
        }
    }

    
 
    
    return $form;

   
}

/*-------- Login ----------*/
add_shortcode('login', 'login');
function login()
{
    if (is_user_logged_in()) {  ?>
        <a class="loginheader" href="<?php echo wp_logout_url(home_url()); ?>">Logout</a><?php } else { ?>
            <a class="loginheader" href="<?php echo esc_url(wp_login_url()); ?>" alt="Login">
                Login
            </a><?php }
}

/*-------- Keranjang Belanja ----------*/
add_shortcode('keranjang_belanja', 'keranjang_belanja');
function keranjang_belanja()
{

    if (isset($_SESSION["cart_item"])) {
        $total_training = count($_SESSION["cart_item"]);
        echo '<a href="' . get_bloginfo('url') . '/keranjang"><p class="keranjang_belanja" data-badge="' . $total_training . '"><i data-feather="shopping-cart"></i></p></a>';
    } else {
        $total_training = 0;
        echo '<a href="' . get_bloginfo('url') . '/keranjang"><p class="keranjang_belanja"><i data-feather="shopping-cart"></i></</p></a>';
    }

    // echo '<script>
    // feather.replace({width: "1em", height: "1em"});    
    // </script>';
}

/*-------- Hapus Sesi ----------*/
add_shortcode('session_des', 'session_des');
function session_des()
{
    myEndSession();
    header('Location: ' . get_bloginfo('url') . '/keranjang');
}

/*-------- Halaman Keranjang ----------*/
add_shortcode('keranjang', 'keranjang');
function keranjang()
{
    ?>
<div class="col-md-8 offset-md-2">

    

<?php

    if (isset($_SESSION["cart_item"])) {     
        $total_peserta = 0;
        $total_harga = 0;
        $url_checkout = get_bloginfo('url') . "/bayar";
        $user = wp_get_current_user();

        $totpeserta = count($_SESSION["cart_item"]);

        if (is_user_logged_in()) {
            $halo = 'Halo, ' .  $user->user_login . '. ';
        } else {
            $halo = '';
        }

        $cart = '';
        
       
        $cart .= '<div class="accordion" id="accordionExample">';

        $cart .= '<p>' . $halo . ' Anda memiliki ' . $totpeserta . ' order pada keranjang belanja. <a href="' . get_bloginfo('url') . '/kosongkan-keranjang" class="">Kosongkan Keranjang</a></p>';
        $i = 0 ;
        foreach ($_SESSION["cart_item"] as $key => $item) {

            $subtotal = $item["jml_peserta"] * $item["harga"];
            
             $cart .= '<div class="accordion-item">
             <h2 class="accordion-header" id="heading'.$i.'">
               <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$i.'" aria-expanded="false" aria-controls="collapse'.$i.'">
                 '. $item["judul"] .'
               </button>
             </h2>
             <div id="collapse'.$i.'" class="accordion-collapse collapse" aria-labelledby="heading'.$i.'" data-bs-parent="#accordionExample">
               <div class="accordion-body">';

               $cart .=  '<form action="" method="POST" class="form_checkout">';
            $cart .=  wp_nonce_field('post_nonce', 'post_nonce_field');    
        
            $cart .= "<p>Masukkan Nama Peserta</p>";
                        for ($i = 1; $i <= $item["jml_peserta"]; $i++) {
                            $cart .= "<p><input class='form-control' style='width:100%;' type='text' name='peserta".$i."' placeholder='Nama Peserta " . $i . "' required>                             
                            <span class='error peserta" . $i. "_empty'></span></p>";
                        }
            $cart .= "Masukkan kode promo<br> <input class='form-control' style='width:20%;' type='text' name='coupon' id='kode_kupon'> <a class='button' style='width:30%;background:green;color:white;' id='cek_kupon'>Cek Kupon</a> 
            <p class='message_kupon' style='padding:5px 0;color:#fbff00; text-align:left;'></p>";            
            $cart .= "<input type='hidden' name='order_id' value='" . session_id() . "'>";
            $cart .= "<input type='hidden' name='harga' value='" . $item["harga"] . "'>";
            $cart .= "<input type='hidden' name='training_id' value='" . $key . "'>";
            $cart .= "<input type='hidden' name='training_title' value='" . $item["judul"] . "'>";
            $cart .= "<input type='hidden' id='total_harga' name='total_harga' value='" . $subtotal . "'>";
            $cart .= "<input type='hidden' id='harga_diskon' name='harga_diskon' value=''>";
            $cart .= "<input type='hidden' name='jml_peserta' value='" . $item["jml_peserta"] . "'>";

            $cart .= "<p><strong>" . $item["judul"] . "</strong><br>             
            Rp</span> " . number_format($item["harga"]) . " x " . $item["jml_peserta"] . " = Rp <span class='harga_asli'><span class='tru'></span>" . number_format($subtotal) . "</span> &nbsp;<span class='harga_baru'></span>                      
           </p>";
            $cart .= "<input type='hidden' name='submitted' value='checkout'>
            <hr style='border-top:1px solid white; padding:10px 0;'>
            <p><button type='submit' class='button button_beli' id='button_checkout'>Konfirmasi Transaksi</button></p>";  
            $cart .= "</form>"; 
                
            $cart .= '</div>
             </div>
           </div>';
                    
        $i++;
        
        }
        $cart .='</div>';      

        echo $cart;
        
        ?>

            <div id='bayar-midtrans'>
                <h3>BayarMIdtrans</h3>
                <?php panggil_midtrans(); ?>
            </div>

    <?php 
    } else {
        echo "<p>Keranjang belanja Anda kosong.</p>";
    } ?>


</div>
    <?php
    
}

/*-------- bayar ----------*
add_shortcode('bayar', 'bayar');
function bayar()
{
    
    // $user = wp_get_current_user();

    // echo '<pre>'; print_r($user); echo '</pre>';

    // $umeta = get_user_meta($user->ID, "telp", TRUE);

    $training_id = isset($_GET['training_id']) ? $_GET['training_id'] : '';

    if ($training_id != '') {
        
        if(isset($_SESSION["cart_item"][$training_id])) {
            $url_checkout = get_bloginfo('url') . "/proses-bayar";

            $jml_peserta = $_SESSION["cart_item"][$training_id]["jml_peserta"];
            $harga = $_SESSION["cart_item"][$training_id]["harga"];
            $total_harga = $jml_peserta * $harga;
            $cart = '';

        // $cart .= '<h5><strong>Pesanan Anda</strong></h5>';

        $cart .= "<div class='container'> 
                   <div class='row' style='background:#0d7d76;'>
                    <div class='col-8'><strong>" . $_SESSION["cart_item"][$training_id]["judul"] . "</strong></div> 
                    <div class='col-4'>
                    Rp</span> " . number_format($harga, 2) . " x " . $jml_peserta . " = Rp " . number_format($total_harga, 2) . "
                    </div>
                </div>
            </div><!-- .container -->
        ";

        $cart .= "<br>
            <div class='container'>               
                <div class='row'>";
                    $cart .= "<div class='col-6'>";
                    // $cart .=  "is login";
                    if (is_user_logged_in()) {

                        $cart .= "<form action='" . $url_checkout . "' method='post'>";
                        $cart .=  wp_nonce_field('post_nonce', 'post_nonce_field');

                        $cart .= "<p><strong>Masukkan Peserta</strong></p>";
                        for ($i = 1; $i <= $jml_peserta; $i++) {
                            $cart .= "<p><input class='form-control' style='width:100%;' type='text' name='peserta[]' placeholder='Nama Peserta " . $i . "' required> </p>";
                        }

                        $cart .= "<p>Masukkan kode promo <input class='form-control' style='width:20%;' type='text' name='coupon' id='kode_kupon' value='XYZ5'> <a class='button' style='width:30%;background:green;' id='cek_kupon'>Cek Kupon</a> </p>";

                        $cart .= "<input type='hidden' name='training_title' value='" . $_SESSION["cart_item"][$training_id]["judul"] . "'>";
                        $cart .= "<input type='hidden' name='training_id' value='" . $training_id . "'>";
                        $cart .= "<input type='hidden' name='total_harga' value='" . $total_harga . "'>";
                        $cart .= "<input type='hidden' name='harga' value='" . $harga . "'>";
                        $cart .= "<input type='hidden' name='jml_peserta' value='" . $jml_peserta . "'>";

                        $cart .= "<input type='hidden' name='submitted' value='bayar'>
                                    <p><button type='submit' class='button button_checkout'>Lanjut ke Pembayaran</button></p>";
                        $cart .= "</form>";
                    }

                $cart .= '</div><!-- .col-6 -->';
        $cart .= '<div class="col-6">';
              

        $cart .= '</div><!-- .col-6 -->';

        $cart .= '</div><!-- .row -->
                </div> <!--.container -->';
        echo $cart;
        } else {
            echo "Sesi telah habis";
            $url = get_bloginfo('url');
            echo("<script>location.href = '".$url."';</script>");
        }
        

    } else {
        echo "kenapa eror";
        ob_start();
        wp_redirect(get_bloginfo('url') . '/keranjang');exit;
    }



    echo '<script>
    feather.replace();    
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    </script>';   

}

/*-------- Tanggal ----------*/
add_shortcode('tanggal', 'tanggal');
function tanggal($atts)
{
    $a = shortcode_atts(array(
        'lokasi' => '',
    ), $atts);

    // $date = new DateTime(get_field('tanggal_mulai'));
    $mulai = get_field('tanggal_mulai');
    $selesai = get_field('tanggal_selesai');

    $e_mulai = explode(' ', $mulai);
    $e_selesai = explode(' ', $selesai);

    if ($a['lokasi'] == 'loop') {
        $tanggal = date('d F Y', strtotime($e_mulai[0]));
    } else {
        if ($e_mulai[0] === $e_selesai[0]) {
            $tanggal = date('d M Y', strtotime($e_mulai[0])) . ' &raquo; ' . date('H:i', strtotime($e_mulai[1])) . '-' . date('H:i', strtotime($e_selesai[1])) . ' WIB';
        } else {
            //jika tanggal beda, cek apakah tahunnya sama;
            if (date('Y', strtotime($e_mulai[0])) === date('y', strtotime($e_selesai[0]))) {

                //jika tanggal beda, cek apakah bulannya sama;
                if (date('M', strtotime($e_mulai[0])) === date('M', strtotime($e_selesai[0]))) {
                    $tanggal = date('d', strtotime($e_mulai[0])) . '-' . date('d M Y', strtotime($e_selesai[0])) . ' &raquo; ' . date('H:i', strtotime($e_mulai[1])) . '-' . date('H:i', strtotime($e_selesai[1])) . ' WIB';
                } else {
                    $tanggal = date('d M Y', strtotime($e_mulai[0])) . '-' . date('d M Y', strtotime($e_selesai[0])) . ' &raquo; ' . date('H:i', strtotime($e_mulai[1])) . '-' . date('H:i', strtotime($e_selesai[1])) . ' WIB';
                }
            } else {
                $tanggal = date('d M Y', strtotime($e_mulai[0])) . '-' . date('d M Y', strtotime($e_selesai[0])) . ' &raquo; ' . date('H:i', strtotime($e_mulai[1])) . '-' . date('H:i', strtotime($e_selesai[1])) . ' WIB';
            }
        }
    }
    return $tanggal;
}

/*-------- Trainer ----------*/
add_shortcode('trainers', 'trainers');
function trainers()
{
    $trainer = get_field('trainer', get_the_ID());
    if ($trainer) {
        echo "<ol class='trainer'>";
        foreach ($trainer as $trainer) {

            echo '<li>' . $trainer->post_title . '</li>';
        }
        echo "</ol>";
    }
}

/*-------- Trainer ----------*/
add_shortcode('total_training', 'total_training');
function total_training()
{
    $count_posts = wp_count_posts($post_type = 'training');

    if ($count_posts) {
        return $published_posts = $count_posts->publish;
    }
}

/*-------- Tempat ----------*/
add_shortcode('cek_running', 'cek_running');
function cek_running($atts)
{
    global $post;
    $a = shortcode_atts(array(
        'lokasi' => 'single',
    ), $atts);

   $running = get_field('running', $post->ID);
   
   if( $running && in_array('yes', $running) ) {    
        $running = 'Running';
    } else {
        $running = 'Scheduled';
    }

    return "<p class='cek_running " . $a['lokasi'] . " " . $running . "'>" . $running . "</p>";
    wp_reset_postdata();
}


/*-------- Tempat ----------*/
add_shortcode('tempat', 'tempat');
function tempat($atts)
{
    $a = shortcode_atts(array(
        'lokasi' => '',
    ), $atts);

    $online = get_field('online', get_the_ID());
    if ($online && in_array('Offline', $online)) {
        if ($a['lokasi'] == 'loop') {
            $tempat = get_field('kota', get_the_ID());
        } else {
            $tempat = get_field('tempat', get_the_ID()) . ' ' . get_field('kota', get_the_ID());
        }
    } else {
        // $tempat = get_field('kota', get_the_ID());
        $tempat = get_field('kota', get_the_ID());
    }

    return $tempat;
}

/*-------- Online ----------*/
add_shortcode('online_button', 'online_button');
function online_button($atts)
{
    $a = shortcode_atts(array(
        'lokasi' => 'single',
    ), $atts);

    $online = get_field('online', get_the_ID());
    if ($online && in_array('Offline', $online)) {
        $online =  "Offline";
    } else {
        // $tempat = get_field('kota', get_the_ID());
        $online = "Online";
    }
    return "<p class='online_button " . $a['lokasi'] . " " . $online . "'>" . $online . "</p>";
}


/*-------- Harga ----------*/
add_shortcode('harga', 'harga');
function harga()
{
    $harga = get_field('harga', get_the_ID());

    return number_format($harga);
}

function status_transaksi($user_id, $post_id) {
    $args = array(
        'post_type'     => 'orders',
        'post_status'   => 'pending',
        'author'   => $user_id,
        'meta_query'    => array (
            'key' => 'training',
            'value' => $post_id,
        ),
    );

    $postslist = get_posts( $args );

    if($postslist) {
        return 1;
    } else {
        return 0;
    }
}
