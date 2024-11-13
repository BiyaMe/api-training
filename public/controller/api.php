<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Api_Training
 * @subpackage Api_Training/public
 * @author     Khalid <khalinoid@gmail.com>
 */
class Api_Training_APIs {

    public function __construct()
    {
        add_shortcode('my_shortcode', [$this, 'api_training_shortcodes']);
    }

    function api_training_shortcodes(){
        ob_start();


        echo '<form method="post" style="text-align:center; margin-top:20px;">

        <input type="text" name="user_input" placeholder="Enter your text" style="padding:10px; width:300px; margin-bottom:10px;">
        <input type="submit" value="Submit" style="padding:10px 20px; cursor: pointer;">
        </form>';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_input'])) {
                $user_input = sanitize_text_field($_POST['user_input']);
                $response = json_decode(make_post_request($user_input), true);

                echo "<h2 style='text-align:center;'>". $response['text'] . ' : ' . $response['sentiment'] . "</h2>";
                echo "<h4 style='text-align:center;'>Confidence: "  . $response['confidence'] . "</h4>";
            }

        return ob_get_clean();
    }
}

function make_post_request($text) {
    $url = 'http://127.0.0.1:5000/predict';
    $args = array(
        'body' => json_encode(array('text' => $text)),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
    );

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        return "Something went wrong: $error_message";
    } else {
        return wp_remote_retrieve_body($response);
    }
}