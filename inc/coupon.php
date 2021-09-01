<?php 

// tabah kolom telpon pada page Users
function new_modify_user_table( $column ) {
    $column['telp'] = 'Telp/WA';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'telp' :
            return  get_user_meta( $user_id, 'telp', true );
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

///////////////////////////////
//          COUPON           //
///////////////////////////////


 // Hook for adding admin menus
 add_action('admin_menu', 'wpcp_coupon_add_pages');
 
 // action function for above hook
 
function wpcp_coupon_add_pages() {
    $menu_slug = 'wpcp-coupon';
     add_menu_page(
        __( 'All Coupons', 'textdomain' ),
        __( 'Coupons','textdomain' ),
        'manage_options',
        $menu_slug,
        'wpcp_coupon_page_callback',
        'dashicons-tag',25
    );
    add_submenu_page( 
        $menu_slug,  
        'Add Coupon',
        'Add Coupon',
        'manage_options',
        'wpcp_coupon_add',
        'wpcp_coupon_add_callback',      
    );
    add_submenu_page( 
        'hide',  
        'Edit Coupon',
        'Edit Coupon',
        'manage_options',
        'wpcp_coupon_edit',
        'wpcp_coupon_edit_callback',      
    );
}


 
/**
 * Disply callback for the Unsub page.
 */

 function wpcp_coupon_page_callback() {
     
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cp_coupon", ARRAY_A);
    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">Coupons</h1>
        <a href="admin.php?page=wpcp_coupon_add" class="page-title-action">Tambah Baru</a>
        <hr class="wp-header-end">

        <table class="wp-list-table widefat fixed striped table-view-list pages" style="margin-top:8px;">
        <thead>
            <tr>
                <th scope="col" id="title" ><span>Program</span></th>                	
                <th scope="col" id="title" ><span>Coupon Code</span></th>                	
                <th scope="col" id="date"><span>Discount</span></th>	
                <!-- <th scope="col" id="date"><span>Type</span></th> -->
                <th scope="col" id="author">Start Date</th>     
                <th scope="col" id="date"><span>End Date</span></th>	
                <th scope="col" id="date"><span>Active</span></th>	
                <th scope="col" id="date"><span>Delete</span></th>	
            </tr>
        </thead>
        <tbody>
            <?php foreach($results as $result) { ?>
            <tr>
                <td><a href="admin.php?page=wpcp_coupon_add&id=<?php echo $result['id']; ?>"><?php echo $result['program']; ?></a></td>
                <td><?php echo $result['code']; ?></td>
                <td><?php echo $result['discount']; ?></td>
                <!-- <td><?php echo $result['type']; ?></td> -->
                <td><?php echo $result['start_date']; ?></td>
                <td><?php echo $result['end_date']; ?></td>
                <td><?php echo $result['active']; ?></td>
                <td>Delete</td>
            </tr>
            <?php } ?>
        </tbody>
        </table>
    
    </div>

     <!-- generate_coupon_code(5, 1); -->

<?php }

function wpcp_coupon_add_callback() { 
    
    if (isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

        $program = $_POST['program'];
        $code = $_POST['code'];
        $discount = $_POST['discount'];
        $type = 1;
        $start_date = strftime('%Y-%m-%d %H:%M:%S', strtotime(sanitize_text_field($_POST['start_date'])));
        $end_date = strftime('%Y-%m-%d %H:%M:%S', strtotime(sanitize_text_field($_POST['end_date'])));
        $active = isset($_POST['aktif']) ? 1 : 0;

        global $wpdb;
        $table = $wpdb->prefix.'cp_coupon';
        $data = array(
            'program' => $program, 
            'code' => $code, 
            'discount' => $discount, 
            'type' => $type, 
            'start_date' => $start_date, 
            'end_date' => $end_date,
            'active' => $active
        );
        $format = array('%s','%s', '%d', '%d', '%s', '%s', '%d');

        if($_POST['submitted'] == 'add') {
            $wpdb->insert($table,$data,$format);
            $my_id = $wpdb->insert_id;

            $url= 'admin.php?page=wpcp_coupon_add&id=' .$my_id;

            // echo("<script>location.href = '".$url."';</script>");
        } else {
            $id = $_POST['id'];
            $where = [ 'id' => $id ];
            $wpdb->update($table,$data,$where, $format);
            $url= 'admin.php?page=wpcp_coupon_add&id=' .$id;

            echo("<script>location.href = '".$url."';</script>");
        }

    } else {

        if(isset($_GET['id'])) {
            $id = $_GET['id'];

            global $wpdb;
            $results = $wpdb->get_row("SELECT *, 
            DATE_FORMAT(start_date, '%Y-%m-%dT%H:%i') AS cstart_date, 
            DATE_FORMAT(end_date, '%Y-%m-%dT%H:%i') AS cend_date  
            FROM {$wpdb->prefix}cp_coupon
            WHERE id= $id ", ARRAY_A);
        }
    ?>

<style>
    .form-control {
        width:100%;
    }
</style>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo (isset($id)) ? 'Edit': 'Add'; ?> Coupon</h1>
    </div>

<div class="container">
    <form action='' method='post'>
        <?php wp_nonce_field('post_nonce', 'post_nonce_field'); 
        
        if(isset($id)) {
            echo '<input type="hidden" name="id" value="' . $id .'">'; 
        }
        ?>
        <div class="row">
            <div class="col-2">
                Program *
            </div>
            <div class="col-7">
                <input type="text" class="form-control" name="program" id="program"  placeholder="Ex: New Year Sale" required value="<?php echo (isset($id)) ? $results['program']: ''; ?>">
            </div>
        </div>    
        <div class="row">
            <div class="col-2">
                Start Date
            </div>
            <div class="col-7">
                <input type="datetime-local" class="form-control" name="start_date" id="start_date"  placeholder="" required value="<?php echo (isset($id)) ? $results['cstart_date']: ''; ?>">
            </div>
        </div>    
        <div class="row">
            <div class="col-2">
                End Date
            </div>
            <div class="col-7">
                <input type="datetime-local" class="form-control" name="end_date" id="end_date"  placeholder="" required value="<?php echo (isset($id)) ? $results['cend_date']: ''; ?>">
            </div>
        </div>    
        <div class="row">
            <div class="col-2">
                Coupon Code *
            </div>
            <div class="col-7">
                <input type="text" class="form-control" name="code" id="code"   value=
                "<?php echo (isset($id) && ($results['code'] != '')) ? $results['code']:  generate_coupon_code(5, 1); ?>" required>
            </div>
        </div>    
        <!-- <div class="row">
            <div class="col-2">
                Type *
            </div>
            <div class="col-7">
                <input type="radio" class="form-control" name="type[]" value="1"> %
                <input type="radio" class="form-control" name="type[]" value="2"> Rp
            </div>
        </div>     -->
        <div class="row">
            <div class="col-2">
                Value Discount*
            </div>
            <div class="col-7">
                <input type="text" class="form-control" name="discount" id="discount"  placeholder="Ex: 50" style="width:60%" required value="<?php echo (isset($id)) ? $results['discount']: ''; ?>"> %
            </div>
        </div>    
        <div class="row">
            <div class="col-2">
                Active
            </div>
            <div class="col-7">
                <input type="checkbox" name="aktif" id="active" <?php echo (isset($id) && $results['active'] == 1) ? 'checked': ''; ?>> click to activate/deactivate this program
            </div>
        </div>    
        <div class="row">
            <div class="col-2">
               
            </div>
            <div class="col-7">
                <input type='hidden' name='submitted' value='<?php echo (isset($id)) ? 'edit': 'add'; ?>'>
                <p><button type='submit' class='button button_checkout'><?php echo (isset($id)) ? 'Edit': 'Add'; ?> Coupon</button></p>
            </div>
        </div>    
       
        
    </form>  
</div>     

<?php
    } //endif submit 
}

/* generate coupon code */
function generate_coupon_code($length, $num) {
    for($i=0; $i < $num; $i++) {
        $randomletter = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTU"), 0, $length);
        echo $randomletter;
    }
 }

 function detailkupon($kupon){
    global $wpdb;
    return $wpdb->get_row("SELECT *, 
        DATE_FORMAT(start_date, '%Y-%m-%dT%H:%i') AS cstart_date, 
        DATE_FORMAT(end_date, '%Y-%m-%dT%H:%i') AS cend_date  
        FROM {$wpdb->prefix}cp_coupon
        WHERE code= '$kupon'", ARRAY_A);
 }
 
add_action( 'wp_ajax_cekkupon', 'cekkupon' );
add_action( 'wp_ajax_nopriv_cekkupon', 'cekkupon' );
function cekkupon() {
    // Change the parameter of check_ajax_referer() to 'jsforwp_likes_nonce'
    check_ajax_referer( 'scajax_nonce' );  
    
    $kupon = sanitize_text_field($_POST['kupon']);
    $subtotal = sanitize_text_field($_POST['total_harga']);
    
    $results = detailkupon($kupon);
       
    if($results) {
        date_default_timezone_set('Asia/Jakarta');
        $today = date('Y-m-d H:i:s');

        if (($today >= $results['start_date']) && ($today <= $results['end_date']) && ($results['quota'] > 0) ){
            $valid= 1;
            $response['discount'] = $results['discount'];

            $mindiskon = $subtotal*($results['discount']/100);
            $diskon = $subtotal - $mindiskon;
            
        } else {
            $valid = 0;  
        }       

    } else {
        $valid = 0; 
    }  
    
    if($valid == 0 ) {
        $message ='Kupon tidak valid';
        $diskon='0';
    } else {
        $message = 'Kupon valid. Anda akan mendapatkan potongan pembayaran sebesar ' . $response['discount'] .'%';
    }

    $response['subtotal_nf'] = number_format($diskon);
    $response['subtotal'] = $diskon;
    $response['valid'] = $valid;
    $response['message'] = $message;
    $response['type'] = 'success';   
  
    $response = json_encode( $response );
    echo $response;
    die();  
}