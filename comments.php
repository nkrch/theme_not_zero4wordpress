<?php

// Handle the comment form submission
function custom_handle_comment_submission($post_id) {
    // Verify nonce for security
    if (!isset($_POST['custom_comment_nonce']) || !wp_verify_nonce($_POST['custom_comment_nonce'], 'custom_comment_action')) {
        echo '<p>Security check failed. Please try again.</p>';
        return;
    }

    // Sanitize and validate input data
    $email = sanitize_email($_POST['email']);
    $rating = intval($_POST['rating']);
    $comment_content = sanitize_textarea_field($_POST['comment']);

    $errors = array();

    if (!is_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Rating must be between 1 and 5.';
    }

    if (empty($comment_content)) {
        $errors[] = 'Please enter a comment.';
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p style="color:red;">' . esc_html($error) . '</p>';
        }
        return;
    }

    // Prepare comment data
    $current_user = wp_get_current_user();

    $commentdata = array(
        'comment_post_ID'      => $post_id,
        'comment_author'       => $current_user->exists() ? $current_user->display_name : 'Anonymous',
        'comment_author_email' => $email,
        'comment_content'      => $comment_content,
        'comment_type'         => '', // '' for regular comments
        'comment_parent'       => 0,
        'user_id'              => $current_user->ID,
        'comment_approved'     => 1, // Auto-approve; set to 0 if you want manual approval
    );

    // Insert the comment into the database
    $comment_id = wp_insert_comment($commentdata);

    if ($comment_id) {
        // Add rating as comment meta
        add_comment_meta($comment_id, 'rating', $rating);
        echo '<p style="color:green;">Thank you for your comment!</p>';
        
    } else {
        echo '<p style="color:red;">There was an error submitting your comment. Please try again.</p>';
    }
}

// Display the custom comment form
function custom_display_comment_form() {
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('custom_comment_action', 'custom_comment_nonce'); ?>
        <p>
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required>
        </p>
        <p>
            <label for="rating">Rating (1-5):</label><br>
            <select name="rating" id="rating" required>
                <option value="">Select Rating</option>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="comment">Comment:</label><br>
            <textarea name="comment" id="comment" rows="5" required></textarea>
        </p>
        <p>
            <input type="submit" name="custom_comment_submit" value="Submit Comment">
        </p>
    </form>
    <?php
}

// Display existing comments with ratings
function custom_display_comments($post_id) {
    $args = array(
        'post_id' => $post_id,
        'status'  => 'approve',
        'orderby' => 'comment_date',
        'order'   => 'DESC',
    );

    $comments = get_comments($args);

   
}


