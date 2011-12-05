<?php
include 'sendmail.php';

add_shortcode( 'mf_contacts', 'mf_contacts');
$message = false;
$mf_post = mf::clean($_POST);


function reset_post(){
	global $mf_post;
	foreach ($mf_post as $k=>$v){
		$mf_post[$k] = "";
	}
}


function mf_contacts(){

	global $mf_plugin, $message, $missed_body, $missed_email, $mf_post;


	$contacts = $mf_plugin->fetch_table($mf_plugin->table_names[0]);
	
	if($_POST['mf_contact_form'] == 1){
		$sent = process_mail();
		if($sent) reset_post();//clears form details
		if($sent) : ?><div class="mail_message success">Message Sent</div><?php else: ?><div class="mail_message error">Sorry, message sending failed.</div><?php endif; ?>
		
			
	<?php }  ?>
	
	<?php if ($message) { ?><div class="mail_message error"><?php echo $message; ?></div><?php } ?>
	
	<form class="mail" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<fieldset> 
			<legend>Your message</legend> 
		
		

		<textarea <?php if($missed_body) echo "class='missed' "; ?> id="body" name="mf_body"><?php echo $mf_post['mf_body']?></textarea>

		<?php if($contacts) : ?>
			<fieldset>
				<legend>What is your message about?</legend> 
				<?php 
			foreach($contacts as $contact) :
				$slug = mf::slugger($contact->section); ?>
				<label for="<?php echo $slug; ?>"><?php echo $contact->section; ?></label><input id="<?php echo $slug; ?>" type="radio" name="mf_question" value="<?php echo $contact->email; ?>" />
			<?php endforeach; ?>
			</fieldset>
		<?php endif ?>
		</fieldset>
		<fieldset>
			<legend>About You</legend>     
		
		
		<input type="hidden" name="mf_contact_form" value="1" />
		<label for="name">Your name</label><input id="name" type="text" name="mf_name" value="<?php echo $mf_post['mf_name']?>"/>

		<label for="email">Your email*</label><input <?php if($missed_email) echo "class='missed' "; ?>id="email" type="text" name="mf_email" value="<?php echo $mf_post['mf_email']?>"/>

		
			
		
		
		</fieldset>  
		
		<input type="submit" value="Send Message" />
	</form>
	<?php
	
}
function validate_form(){
	global $message, $missed_body, $missed_email, $mf_post;
	
	if($mf_post['mf_body'] == "") {
		$message = "You have not told us your message";
		$missed_body = true;
	}
	if($mf_post['mf_email'] == "") {
		$message = ($missed_body ? $message." or " : "Please tell us ")."your email address";
		$missed_email = true;

		
	}
	if($missed_email || $missed_body){
		return false;
	}	
	if(!preg_match('/^([0-9a-zA-Z]([-\.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/',$mf_post['mf_email'])){
		$message = "Please include a valid email address";
		$missed_email = true;
	}
	
	return true;
}

function process_mail(){
	global $mf_post;
	if(!validate_form()) return false;
	if(!$mf_post['mf_question'] ||$mf_post['mf_question'] == "") $mf_post['mf_question'] = get_bloginfo('admin_email');
	$message = "Name : ".$mf_post['mf_name']."<br />Reply Address: ".$mf_post['mf_email']."<div style='width: 100%; border-top: 1px solid #555555; margin: 14px 0;'></div>Message: <br />".$mf_post['mf_body'];
	
	return smtpmailer($mf_post['mf_question'], $mf_post['mf_email'], ($mf_post['mf_name'] ? $mf_post['mf_name'] : ""), "Website Feedback", $message);	
}

?>