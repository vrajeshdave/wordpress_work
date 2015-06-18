    <?php

    /***********DISPLAY COMMENT IN COMMENT FORM*******************************/

    function display_comment_image1( $comments ) {

    // Make sure that there are comments
    if( count( $comments ) > 0 ) {

    // Loop through each comment…
    foreach( $comments as $comment ) {
    if( true == get_comment_meta( $comment->comment_ID, ‘comment_image’ ) ) {
    // …get the comment image meta
    $comment_image = get_comment_meta( $comment->comment_ID, ‘comment_image’, true );
    $image_attributes = wp_get_attachment_image_src( $comment_image ,’thumbnail’);
    if(!empty($image_attributes [0])){
    $comment->comment_content .= ‘<p class=”comment-image”>';
    $comment->comment_content .= ‘<img src=”‘ . $image_attributes[0] . ‘” alt=”” />';
    $comment->comment_content .= ‘</p><!– /.comment-image –>';
    }
    }
    if( true == get_comment_meta( $comment->comment_ID, ‘comment_video_link’ ) ) {

    //Get VIDEO LINK
    $comment_vlink = get_comment_meta( $comment->comment_ID, ‘comment_video_link’, true );
    if(!empty($comment_vlink)){
    $comment->comment_content .= ‘<a href=”‘.$comment_vlink.'” taget=”_black”>read more</a>';
    }
    // …and render it in a paragraph element appended to the comment
    }

    } // end foreach

    }

    return $comments;

    } // end display_comment_image

    /********ADD  FIELD IN COMMENT FORM *****************/
    add_filter( ‘comments_array’, ‘display_comment_image1′ );

    function add_image_upload_form1( $post_id ) {

    // Create the label and the input field for uploading an image
    //if ( ‘disabled’ != get_option( ‘comment_image_toggle_state’ ) && ‘disable’ != get_post_meta( $post_id, ‘comment_images_toggle’, true ) ) {

    $html = ‘<input type=”file” id=”upload_photo” name=”upload_image” accept=”image/x-png, image/gif, image/jpeg” style=”display:none”/>';
    $html.='<input  id=”video_link” type=”text” name=”video_link” style=”display:none;margin:10px 0″/>';
    echo $html;

    //         } // end if

    }
    add_action( ‘comment_form’ , ‘add_image_upload_form1′ );

    //add_action(‘comment_post ‘, ‘toto’);

    /***********SOVE COMMENT FIELD AFTER save BUTTOM CLICKED*******************************/

    function save_comment_image12( $comment_id ) {
    global $post;
    if(isset($_REQUEST[‘video_link’]) && !empty($_REQUEST[‘video_link’]))
    {
    add_comment_meta($comment_id,’comment_video_link’,$_REQUEST[‘video_link’]);
    }
    if(isset($_FILES[‘upload_image’]) && is_user_logged_in()){

    $uploadedfile = $_FILES[‘upload_image’];
    //$upload_name = $_FILES[‘upload_image’][‘name’].’_’.rand();
    $upload_name = $_FILES[‘upload_image’][‘name’];

    $uploads = wp_upload_dir();
    $filepath = $uploads[‘path’].”/$upload_name”;

    if ( ! function_exists( ‘wp_handle_upload’ ) )
    {
    require_once( ABSPATH . ‘wp-admin/includes/file.php’ );

    }
    require_once( ABSPATH . ‘wp-admin/includes/image.php’ );
    $upload_overrides = array( ‘test_form’ => false );
    //$attach_id = media_handle_upload( $file, $new_post );
    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
    //print_r($movefile);
    if ( $movefile && !isset( $movefile[‘error’] ) ) {

    $file = $movefile[‘file’];
    $url = $movefile[‘url’];
    $type = $movefile[‘type’];

    //media_handle_upload( $file_handler, 0 );
    $attachment = array(
    ‘post_mime_type’ => $type ,
    ‘post_title’ => $upload_name,
    ‘post_content’ => ‘Image for ‘.$upload_name,
    ‘post_status’ => ‘inherit’,
    ‘post_parent’ => $post->ID
    );

    $attach_id=wp_insert_attachment( $attachment, $file, $post->ID);
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id,  $attach_data );
    //echo $postid = wp_insert_post( $my_post );

    //add_post_meta($postid, “file”, $file);
    $stack= array($attach_id);
    $gallery_old = get_post_meta($post->ID,’p_gallery’,true);
    $result = array_merge($stack, $gallery_old);
    //echo $updated_gallery = serialize($result);
    update_post_meta($post->ID,’p_gallery’,$result);
    } else {
    echo $movefile[‘error’];
    }
    //$attachid_arr = array($attach_id);
    add_comment_meta($comment_id,’comment_image’,$attach_id);
    }

    } // end save_comment_image

    add_filter( ‘wp_insert_comment’, ‘save_comment_image12′  );
