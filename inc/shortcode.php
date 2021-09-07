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

        $form .= '<div id="load" class="p-2">
        <form action="' . $url . '" method="POST" class="pendaftaran p-3">
        <h5 class="text-center">Beli Program Ini</h5>
        <input type="hidden" name="url" id="url" class="" value="'. get_the_permalink() .'">';

        $form .= '<p style="text-align:center;"><a href="' . esc_url(wp_login_url(get_permalink() . '/?training_id='.  get_the_ID())) . '">Login disini </a>jika sudah terdaftar.</p>';

        $form .= "<div id='daftar_error'><span class='alert alert-danger d-block'>Error! Periksa kembali.</span></div>";
        
        $form .= ' 
        <div class="row mb-2">
            <label for="email" class="col-sm-4 col-form-label">Email*</label>
            <div class="col-sm-8">
                <input type="text" name="email" id="email" class="form-control" value="">
                <span class="invalid-feedback error email_empty email_invalid email_used"></span>
            </div>
        </div>
        <div class="row mb-2">
            <label for="password" class="col-sm-4 col-form-label">Password*</label>
            <div class="col-sm-8">
                <input type="password" name="password" id="password" class="form-control" value="">
                <span class="invalid-feedback error password_thooshort password_empty"></span>
            </div>
        </div>
        <div class="row mb-2">
            <label for="telp" class="col-sm-4 col-form-label">Telp/WA* </label>
            <div class="col-sm-8">
                <input type="text" name="telp" id="telp" class="form-control" value="" placeholder="Contoh : 085612344567">
                <span class="invalid-feedback error telp_thooshort telp_notnumeric"></span>
            </div>
        </div>';

        $form .= "<input type='hidden' name='submitted' value='daftar'>";     
        
        $form .= '<div class="row">
            <label for="jml_peserta" class="col-sm-4 col-form-label">Jumlah Peserta* </label>
            <div class="col-sm-8">
                <input type="number" name="jml_peserta" id="jml_peserta" class="form-control" value="1" min="1" value="1">
            </div>
        </div>';   
 
        $form .= '<input type="hidden" name="harga" id="harga" value="' . get_field('harga', get_the_ID()) . '" />';
        $form .= '<input type="hidden" name="post_id" id="post_id" value="' . get_the_ID() . '" />'; 
        $form .= '<div class="form-group row mt-3">
                <label for="button" class="col-sm-4 col-form-label d-none d-sm-block">&nbsp;</label>
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
            $form .= '<div id="load">
            <form action="' . $url . '" method="POST" class="pendaftaran p-3">
            <h5 class="text-center">Beli Program Ini</h5>
            <input type="hidden" name="url" id="url" class="" value="'. get_the_permalink() .'">';
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
                    <label for="button" class="col-sm-4 col-form-label d-none d-sm-block">&nbsp;</label>
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

            
            foreach ($status_transaksi as $post) {  
               
                $jml_peserta = get_field('jml_peserta', $post->ID);  
                $harga_diskon = get_field('harga_diskon', $post->ID);  
                $total_harga = ($harga_diskon != '') ? $harga_diskon : get_field('total_harga', $post->ID);  
                $status_bayar = get_field('status_bayar', $post->ID);       
    
                $nonce = wp_create_nonce( 'scajax_nonce' );
                // $total_harga = $field["harga"]*$field["jml_peserta"];
                $html = '';               

                if($status_bayar == 'tambah_peserta') {
                    $html .= '<div id="load" class="p-3"><h5 class="text-center mb-3">Selesaikan Pesanan Anda</h5>';
                    // $html .= "<p>Masukkan Nama Peserta</p>";
                    $html .=  '<form action="" method="POST" class="form_checkout">';
                    $html .=  '<input type="hidden" name="nonce" value="'. $nonce .'">';
                    $html .=  '<input type="hidden" name="total_harga" id="total_harga" value="'. $total_harga .'">';
                    $html .=  '<input type="hidden" name="jml_peserta" id="jml_peserta" value="'. $jml_peserta .'">';
                    for ($i = 1; $i <= $jml_peserta; $i++) {
                    $html .= '<div class="mb-2 row">
                            <label for="nama peserta" class="col-sm-4 col-form-label">Nama Peserta '.$i.'*</label>
                            <div class="col-sm-8">
                            <input type="text" name="peserta'.$i.'" class="form-control"  value="" required>
                            <span class="invalid-feedback error peserta' . $i. '_empty"></span>
                            </div>
                        </div>';
                    }
                    $html .='<div class="mb-2 row">
                        <label for="nama peserta" class="col-sm-4 col-form-label">Punya kupon?</label>
                        <div class="col-sm-8">    
                        <div class="input-group mb-3">              
                            <input type="text" class="form-control" name="coupon" id="kode_kupon" style="width:100px !important;">
                            <span class="btn btn-info" style="border-top-right-radius:5px;border-bottom-right-radius:5px" type="button" id="cek_kupon">
                            Cek Kupon</span>
                            <span class="invalid-feedback message_kupon d-block" ></span>
                        </div>
                        </div>
                    </div>';
            
                    $html .= "<p><strong>" . $post->post_title . "</strong><br>             
                        Rp</span> " . number_format($total_harga) . " x " . $jml_peserta . " = Rp <span class='harga_asli'><span class='tru'></span>" . number_format($total_harga) . "</span> &nbsp;<span class='harga_baru'></span>                      
                    </p>";
                    $html .= "<input type='hidden' id='training_id' name='training_id' value='". $post->ID ."'>";
                    $html .= "<input type='hidden' id='training_title' name='training_title' value='". $post->post_title ."'>";
                    $html .= '<input type="hidden" name="url" id="url" class="" value="'. get_the_permalink() .'">';
                    $html .= "<input type='hidden' id='harga_diskon' name='harga_diskon' value=''>";
                    $html .= "<input type='hidden' name='submitted' value='checkout'>
                        <hr style='border-top:1px solid white; padding:10px 0;'>
                        <p><button type='submit' class='button button_beli btn btn-warning' id='button_checkout'>
                            <span class='spinner-border text-light spinner-border-sm d-none' role='status' aria-hidden='true'></span>
                                <span class='visually-hidden'>Loading...</span>
                        Konfirmasi Pesanan</button></p>";  
                    $html .= "</form></div>"; 
                } else if($status_bayar == 'belum_bayar') { // status_bayar 

                    $transaksi = array(
                        'post_id' => $post->ID,
                        'post_title' => $post->post_title,
                        'total_harga' => $total_harga,
                        'jml_peserta' => $jml_peserta,
                    );

                    $snapToken = get_midtrans($transaksi);

                $html .= '<div id="bayar-midtranss" class="p-3"> 
                <h5>Pesanan Anda</h5>
                <p>Training : ' . $post->post_title . ' (' . $jml_peserta . ' peserta)</p>
                <p>Rp: ' . number_format($total_harga) . '</p>
                <button id="pay-button" class="btn btn-warning btn-lg btn-block">Bayar Sekarang</button>

                <div id="bayar-sukses"></div>
                <div id="result-json"></div>
                ';
                $html .= '
                <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-NUHDTW6uipcvE7sz"> </script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
                <script type="text/javascript"> 
                $("#pay-button").on("click", function() {                 
                    snap.pay("' . $snapToken . '", {
                
                        onSuccess: function(result){  
                      
                            $.ajax( {
                                type : "post",
                                dataType : "json",
                                url : scajax_globals.ajax_url,
                                data : {
                                action: "transaksi",
                                kode_transaksi : 200,
                                result : result,
                                order_id : ' . $post->ID . ',
                                _ajax_nonce: scajax_globals.nonce
                                },
                                beforeSend: function ( xhr ) {
                                console.log("Loading ...")                           
                                
                                },
                                success: function( response ) {
                                    if( "success" == response.type ) {
                         
                                        console.log(response)
                                        $("#pay-button").hide();
                                        $("#bayar-sukses").html("Terima kasih, pembayaran Anda berhasil.");
                                    }
                                    else {
                                       alert( "Error" );
                                    }
                                 },                  
                            });  

                        },
                        onPending: function(result) {
                        document.getElementById("result-json").innerHTML += JSON.stringify(result, null, 2)
                        alert("pending");
                        },
                        onError: function(result) {
                        document.getElementById("result-json").innerHTML += JSON.stringify(result, null, 2)
                        alert("gagal");
                        }
                    });

                    
                });    
                     
            </script></div>';
                } elseif($status_bayar == 'lunas') {

                    $html .= "<div class='d-block py-3 px-3'><span class='alert alert-warning'><i class='fas fa-exclamation-triangle'></i> Anda sudah terdaftar di program ini</span></div>";
                    
                }// belum bayar
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
        'meta_query'    => array(
            'compare' => 'AND',
            array (
                'key' => 'training',
                'value' => $post_id,
                'compare' => '=',
            ),           
        ),
    );

    $postslist = get_posts( $args );

    if($postslist) {      

        return $postslist;

    } else {

        return 0;
        // echo "gada transaksi";
    }
}
