
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
            