<?php
/**
 * Custom script to generate 1 million random users
 */

// Set the maximum execution time and memory limit to handle the large import
set_time_limit(0);
ini_set('memory_limit', '2048M');

// Include WordPress core files for database access
require_once('wp-load.php');

// Function to generate a random username
function generate_username() {
    return 'user_' . wp_generate_password(8, false);
}

// Function to generate a random email
function generate_email($username) {
    return $username . '@example.com';
}

// Function to generate a random password
function generate_password() {
    return wp_generate_password(12, false);
}

// Define the number of users to generate
$user_count = 1000000;

// Generate and insert users
for ($i = 0; $i < $user_count; $i++) {
    $username = generate_username();
    $email = generate_email($username);
    $password = generate_password();

    // Insert user directly into the database using a custom query
    $wpdb->insert(
        $wpdb->users,
        array(
            'user_login' => $username,
            'user_pass' => wp_hash_password($password),
            'user_email' => $email,
            'user_registered' => current_time('mysql'),
            'user_status' => 0,
        ),
        array('%s', '%s', '%s', '%s', '%d')
    );

    // Retrieve the user ID of the newly inserted user
    $user_id = $wpdb->insert_id;

    // Assign a role to the user (e.g., 'subscriber')
    $wpdb->insert(
        $wpdb->usermeta,
        array(
            'user_id' => $user_id,
            'meta_key' => $wpdb->prefix . 'capabilities',
            'meta_value' => serialize(array('subscriber' => true)),
        )
    );
}

echo 'User generation completed.';
