<?php

/* Shortcode Handle
 ---------------------------------------------------------------------- */
function AnyContact_ShortCode($args = false) {
	
	// fix the wordpress slashit
	array_walk_recursive($_POST,'stripslashes_fix');
	
  if(!$args || $args == "" || !array_key_exists('id',$args) || !is_numeric($args['id'])) {
    return AnyContact_Frontend_Error(__('Ihr Formular wurde falsch konfiguriert. Bitte überprüfen Sie Einstellungen und den Shortcode.','anycontact'));
  }
  return AnyContact_Frontend_HTML($args['id']);
}

function AnyContact_Frontend_CheckPost() {
	
	if(!array_key_exists('pac_id',$_POST)) {
		return AnyContact_Frontend_Error(__('Die übermittelte ID enthält leider kein Formular.','anycontact'));
	}
	
	$id = $_POST['pac_id'];
	unset($_POST['pac_id']);
	
  $post = get_post($id,'ARRAY_A');
	
	if(!is_array($post) || sizeof($post) == 0) {
    return AnyContact_Frontend_Error(__('Die übermittelte ID enthält leider kein Formular.','anycontact'));
  }
  $post_args = json_decode($post['post_content'],true);
	
	#$regexp_url = '';
	#$regexp_email = '';
	$regexp_date = '/^[0-9\/\-\.]+$/';
	$regexp_number = '/^\d+$/';
	$regexp_telephon = '/^[0-9\+\(\)\/\-\s]+$/';
	$regexp_character = '/^\D+$/';
	$error = array();
	
	foreach($post_args['field'] AS $key => $args) {
		$name = $args['type'].'_'.$key;
		$val = $_POST[$name];
		$val_len = strlen($_POST[$name]);
		
		if($args['type'] == 'captcha') {
			if($_SESSION['captcha_code'] != $val) {array_push($error,$name);}
		}
		else if(array_key_exists('required',$args) && $args['required'] == 'true' || in_array($args['type'],array('select'))) {
			
			if($val_len == 0) { array_push($error,$name); continue; }
			
			if(!in_array($args['type'],array('captcha','select'))) {
				
				if(array_key_exists('length_min',$args) && is_numeric($args['length_min'])) {
					if($val_len < $args['length_min']) { array_push($error,$name); continue; }
				}
				
				if(array_key_exists('length_max',$args) && is_numeric($args['length_max'])) {
					if($val_len > $args['length_max']) { array_push($error,$name); continue; }
				}
			}
			
			switch($args['type']) {
				case 'character': if(!preg_match($regexp_character,$val))  			array_push($error,$name); continue 2;
				case 'date':			if(!preg_match($regexp_date,$val))						array_push($error,$name); continue 2;
				case 'mail':      if(!filter_var($val,FILTER_VALIDATE_EMAIL))   array_push($error,$name); continue 2;
				case 'number':    if(!preg_match($regexp_number,$val))     			array_push($error,$name); continue 2;
				case 'tel':       if(!preg_match($regexp_telephon,$val))   			array_push($error,$name); continue 2;
				case 'url':       if(!filter_var($val,FILTER_VALIDATE_URL))     array_push($error,$name); continue 2;
			}
			
		} else {
			
			if($val_len > 0) {
				
				if(!in_array($args['type'],array('select'))) {
				
					if(array_key_exists('length_min',$args) && is_numeric($args['length_min'])) {
						if($val_len < $args['length_min']) { array_push($error,$name); continue; }
					}
					
					if(array_key_exists('length_max',$args) && is_numeric($args['length_max'])) {
						if($val_len > $args['length_max']) { array_push($error,$name); continue; }
					}
				}
				
				switch($args['type']) {	
					case 'character': if(!preg_match($regexp_character,$val))  			array_push($error,$name); continue 2;
					case 'date':			if(!preg_match($regexp_date,$val))						array_push($error,$name); continue 2;
					case 'mail':      if(!filter_var($val,FILTER_VALIDATE_EMAIL))   array_push($error,$name); continue 2;
					case 'number':    if(!preg_match($regexp_number,$val))     			array_push($error,$name); continue 2;
					case 'tel':       if(!preg_match($regexp_telephon,$val))   			array_push($error,$name); continue 2;
					case 'url':       if(!filter_var($val,FILTER_VALIDATE_URL))     array_push($error,$name); continue 2;
				}
			}
		}
	}
	return sizeof($error) > 0?$error:true;
}


function AnyContact_Frontend_HTML($id) {
	
  $post = get_post($id,'ARRAY_A');
	
	if(!is_array($post) || sizeof($post) == 0) {
    return __('Die übermittelte ID enthält leider kein Formular.','anycontact');
  }
	
  $form_args = json_decode($post['post_content'],true);
	
	$output .= '<!-- START WP-Plugin AnyContact http://any-contact.com -->';
	$output .= '<div id="pac-form-css">';

    $error = array();
		$form_load = true;
    
    if($_POST) {
			
      $check_post = AnyContact_Frontend_CheckPost();
			
      if($check_post === true) {
				
				if(array_key_exists('email',$form_args['setting']) && strlen($form_args['setting']['email']) > 5) {
					$to = $form_args['setting']['email'];
				} else {
					$to = get_option('admin_email');
				}
				
				$subject = $form_args['setting']['email_subject'];
				if(!$subject || strlen($subject) == 0) $subject = _e('Anfrage Kontaktformular','anycontact');
				
				$message = '<html>
											<head></head>
											<body>
												<table>';
				
				foreach($form_args['field'] AS $key => $args) {
					if($args['type'] == 'captcha') continue;
					$name = $args['type'].'_'.$key;
					$message .= '<tr>
													<td>'.$args['title'].'</td>
													<td>'.$_POST[$name].'</td>
												</tr>';
				}
				
				$message .= '   </table>
											</body>
										 </html>';
				
				$headers = array();
				$headers[]  = "MIME-Version: 1.0" . "\r\n";
				$headers[] = "Content-type: text/html; charset=utf-8" . "\r\n";
				$headers[] = "From: AnyContact <anycontact@".$_SERVER['SERVER_NAME'].">" . "\r\n";
				$headers[] = "Subject: {$subject}";
				$headers[] = "X-Mailer: PHP/".phpversion();
				
        $send_message = wp_mail($to,$subject,$message,$headers);
				
				if($send_message === true) {
					$output .= AnyContact_Frontend_SuccessBox($form_args['text']['success']);
					$form_load = false;
				} else {
					$output .= AnyContact_Frontend_ErrorBox($form_args['text']['error_code']);
				}
      } else {
				$error = $check_post;
				$output .= AnyContact_Frontend_ErrorBox($form_args['text']['error_form']);
      }
    }
		
		if($form_load) {
			
			$output .= '<form id="pac-form-css" method="post" action="">
										<input id="pac_form_id" name="pac_id" type="hidden" value="'.$id.'" />';
										
				foreach($form_args['field'] AS $key => $args) {
					$name = $args['type'].'_'.$key;
					
					$output .= '<p>
												<label for="'.$name.'"><strong>'.$args['title'].(($args['required']=='true' || $args['type'] == 'captcha')?'*':false).'</strong></label> ';
												
						if(in_array($name,$error)) {
							$output .= AnyContact_Frontend_Error($form_args['text']['error_'.$args['type']]);
							$class = 'class="pac-error-input"';
						}
					
					$output .= '</p>
											<p class="pac">';
					
						switch($args['type']) {
							
							case 'captcha':
								$output .= '<img src="'.ANYCONTACT_HTTP.'captcha.php" title="Captcha" />';
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="text" value="" autocomplete="off" required />';
								break;
								
							case 'character':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="text" value="'.$_POST[$name].'" '.(($args['required']=='true')?'required':false).'/>';
								break;
								
							case 'checkbox':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="checkbox" value="checked" '.($_POST[$name] == 'checked'?'checked':false).(($args['required']=='true')?'required':false).'/>';
								if(array_key_exists('link_title',$args) && array_key_exists('link_url',$args)) {
									$output .= '<span><a href="'.$args['link_url'].'" target="_blank">'.$args['link_title'].'</a></span>';
								}
								/*<input name="checkbox[]" type="hidden" value="<?= $name;?>" />*/
								break;
								
							case 'date':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="date" value="'.$_POST[$name].'" '.(($args['required']=='true')?'required':false).'/>';
								break;
								
							case 'mail':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="email" value="'.$_POST[$name].'" '.(($args['required']=='true')?'required':false).'/>';
								break;
								
							case 'number':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="number" value="'.$_POST[$name].'" '.(($args['required']=='true')?'required':false).'/>';
								break;
							
							case 'select':
								$o = explode(',',$args['options']);
								$output .= '<select '.$class.' id="'.$name.'" name="'.$name.'" '.(($args['required']=='true')?'required':false).'>';
								foreach($o AS $val) {
									$output .= '<option value="'.$val.'" '.($_POST['name'] == $val?'selected':false).'>'.$val.'</option>';
								}
								$output .= '</select>';
								break;
							
							case 'tel':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="tel" value="'.$_POST[$name].'" '.(($args['required']=='true')?'required':false).'/>';
								break;
							
							case 'text':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="text" value="'.$_POST[$name].'" '.(($args['required']=='true')?'required':false).'/>';
								break;
							
							case 'textarea':
								$output .= '<textarea '.$class.' id="'.$name.'" name="'.$name.'" '.(($args['required']=='true')?'required':false).'>'.$_POST[$name].'</textarea>';
								break;
							
							case 'url':
								$output .= '<input '.$class.' id="'.$name.'" name="'.$name.'" type="url" value="'.$_POST[$name].'" '.(($args['required']=='true')?'required':false).'/>';
								break;
						}
					$output .= '</p>
										<div class="divider-10"></div>';
					unset($class);
				}
				$output .= '<p class="pac">
									<input type="submit" value="'.$form_args['text']['button_submit'].'" />
								</p>
							</form>';
		}
	$output .= '</div>';
	$output .= '<!-- END WP-Plugin AnyContact http://any-contact.com -->';
	
	return $output;
}

function AnyContact_Frontend_SuccessBox($msg) {
	return '<span class="pac-success-box">'.$msg.'</span>';
}

function AnyContact_Frontend_ErrorBox($msg) {
	return '<span class="pac-error-box">'.$msg.'</span>';
}

function AnyContact_Frontend_Error($msg) {
	return '<span class="pac-error-msg">'.$msg.'</span>';
}

?>