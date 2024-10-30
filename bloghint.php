<?php
/*
Plugin Name: BlogHint
Author: Jake Kring
Description: This plugin provides detailed, aggregate level analytics on your commenters and viewers. How old is your average reader?
What social networks do they frequent? Where are they from? What are their interests? And when did they visit your blog?
BlogHint gives insight into these details and more!
License: GPL2
*/
function query_api() {
	$email = $_POST['email'];
	if ($email == ''){
	    global $user_email;
	    get_currentuserinfo();
	    $email = $user_email;
	}
	global $post;
	$postTitle = urlencode(get_the_title($post->ID));
	$at = strpos($email, "@");
	$domain = substr($email, $at+1);
	$hashedEmail = hash("md5", $email);

	$url = 'http://dev.userhint.com/bloghint';
	$postString = '?email='.$hashedEmail.'&domain='.$domain.'&site='.urlencode(get_bloginfo()).'&url='.get_bloginfo('url').'&post='.urlencode($postTitle);
	
	$curlPost = curl_init();
	curl_setopt($curlPost, CURLOPT_URL, $url.$postString);
	
	ob_start();
	curl_exec($curlPost);
	ob_end_clean();
	curl_close($curlPost);
}

function bloghint_add_pages() {

    add_menu_page('BlogHint', 'BlogHint', 8, __FILE__, 'highlights');
    add_submenu_page(__FILE__, 'Age', 'Age', 8, 'age', 'age');
    add_submenu_page(__FILE__, 'Gender', 'Gender', 8, 'gender', 'gender');
    add_submenu_page(__FILE__, 'Location', 'Location', 8, 'location', 'location');
    add_submenu_page(__FILE__, 'Email Domains', 'Email Domains', 8, 'domains', 'domains');
    add_submenu_page(__FILE__, 'Social Networks', 'Social Networks', 8, 'networks', 'networks');
    add_submenu_page(__FILE__, 'Viewer Interests', 'Viewer Interests', 8, 'interests', 'interests');
}


function highlights() {
    $highlights = file_get_contents("http://dev.userhint.com/report?on=".urlencode(get_bloginfo())."&about=highlights");
    echo $highlights;
}

function age() {
    echo "<h2>Age</h2>";
    $age = file_get_contents("http://dev.userhint.com/report?on=".urlencode(get_bloginfo())."&about=age");
    echo $age;
}


function gender() {
    echo "<h2>Gender</h2>";
    $gender = file_get_contents("http://dev.userhint.com/report?on=".urlencode(get_bloginfo())."&about=gender");
    echo $gender;
}

function location() {
    echo "<h2>Location</h2>";
    $location = file_get_contents("http://dev.userhint.com/report?on=".urlencode(get_bloginfo())."&about=location");
    echo $location;
}

function domains() {
    echo "<h2>Email Domains</h2>";
    $domains = file_get_contents("http://dev.userhint.com/report?on=".urlencode(get_bloginfo())."&about=domains");
    echo $domains;
}

function networks() {
    echo "<h2>Social Networks</h2>";
    $networks = file_get_contents("http://dev.userhint.com/report?on=".urlencode(get_bloginfo())."&about=networks");
    echo $networks;
}

function interests() {
    echo "<h2>Viewer Interests</h2>";
    $interests = file_get_contents("http://dev.userhint.com/report?on=".urlencode(get_bloginfo())."&about=interests");
    echo $interests;
}

function add_cookie_reader()
{
?>
<script type="text/javascript" src="http://lg.rlcdn.com/rd?type=redir&site=43991&url=http://dev.userhint.com/bloghint/track_reader?blog=<?php echo urlencode(get_bloginfo()) ?>"></script>
<?php
}

function unique_viewer_test()
{
	if(empty($_COOKIE['BlogHintVisited'])){
		setcookie('BlogHintVisited','1',(time()+24*3600));
		add_action('wp_head', 'add_cookie_reader');
    }
    else remove_action('wp_head', 'add_cookie_reader');
    
}

add_action('init','unique_viewer_test');
add_action('comment_post', 'query_api');
add_action('admin_menu', 'bloghint_add_pages');

?>