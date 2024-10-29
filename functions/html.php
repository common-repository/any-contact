<?php

/* Shortcode Handle
 ---------------------------------------------------------------------- */
function AnyContact_HTML() {
  
  switch($_GET['action']) {
    #case 'new':   AnyContact_HTML_NewForm(); break;
    case 'edit':  AnyContact_HTML_EditForm(); break;
    default:      AnyContact_HTML_Overview();
  }
  return;
}

function AnyContact_HTML_Overview() {  
  ?>
  <div class="wrap" id="plugin-anycontact">
    <h2>
			<?php _e('AnyContact &raquo; Übersicht','anycontact');?>
			<a title="<?php _e('Neues Formular erstellen','anycontact');?>" href="<?= AnyContact_AdminLink(array('page' => 'anycontact-new'));?>" class="add-new-h2"><?php _e('Neues Formular erstellen','anycontact');?></a>
		</h2>
    <p><?php _e('Fügen Sie den Shortcode in die gewünschte Seite oder den gewünschten Beitrag ein.<br />Das Formular wird dann automatisch mit den gewünschten Einstellungen geladen.','anycontact');?></p>
		<?
		if(array_key_exists('action',$_GET) && array_key_exists('id',$_GET) && $_GET['action'] == 'delete') {
			$wp_delete_post = wp_delete_post($_GET['id'],true);
			if($wp_delete_post) {
				?>
				<div class="updated"><p><strong><? _e('Formular wurde erfolgreich gelöscht.','anycontact');?></strong></p></div>
				<?
			}
		}
		?>
    <table class="wp-list-table widefat fixed pages">
      <thead>
        <tr>
          <th scope="col" id="title" class="manage-column column-title"><? _e('Name','anycontact');?></th>
          <th scope="col" id="title" class="manage-column column-title"><? _e('Shortcode','anycontact');?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th scope="col" id="title" class="manage-column column-title"><? _e('Name','anycontact');?></th>
          <th scope="col" id="title" class="manage-column column-title"><? _e('Shortcode','anycontact');?></th>
        </tr>
      </tfoot>
      <tbody id="the-list">
        <?php
				$posts = get_posts(array('post_type' => 'anycontact', 'order' => 'ASC'));
        if(is_array($posts) && sizeof($posts) > 0) {
          $counter = 1;
          foreach($posts AS $key => $val) {
            ?>
            <tr class="<?= $counter%2==0?'alternate':false;?>">
              <td class="">
                <strong>
                  <a class="" href="<?= AnyContact_AdminLink(array('page' => 'anycontact', 'action' => 'edit', 'id' => $val->ID));?>" title="<?= $val->post_title; ?>"><?= $val->post_title; ?></a>
                </strong>
                <div class="locked-info">
                  <span class="locked-avatar"></span>
                  <span class="locked-text"></span>
                </div>
                <div class="row-actions">
                  <span class="">
                    <a href="<?= AnyContact_AdminLink(array('page' => 'anycontact', 'action' => 'edit', 'id' => $val->ID));?>" title="<? _e('Dieses Formular umbennen','anycontact');?>"><? _e('Bearbeiten','anycontact');?></a> |
                  </span>
                  <span class="inline">
                    <a href="<?= AnyContact_AdminLink(array('page' => 'anycontact', 'action' => 'delete', 'id' => $val->ID));?>" class="editinline" title="<? _e('Dieses Formular löschen','anycontact');?>"><? _e('Löschen','anycontact');?></a>
                  </span>
                </div>
              </td>
              <td>
                [anycontact id="<?= $val->ID;?>"]
              </td>
            </tr>
            <?
            $counter++;
          }
        } else {
          ?>
          <tr>
            <td class="post-title page-title column-title">
              <strong>
                <?php _e('Keine Formulare gefunden..','anycontact'); ?>
              </strong>
            </td>
            <td></td>
          </tr>
          <?
        }
        ?>
      </tbody>
    </table>
  </div>
  <?
}

function AnyContact_HTML_NewForm() {
	$next_form_number = sizeof(get_posts(array('post_type' => 'anycontact')))+1;
  ?>
  <div class="wrap" id="plugin-anycontact">
    <h2><?php _e('AnyContact &raquo; Neues Formular','anycontact');?></h2>
    <br />
		<div id="p-ac-placeholder-for-message"></div>
		<noscript>
			<div class="error">
				<p>
					<strong><? _e('Bitte aktivieren Sie Javascript um das Plugin nutzen zu können. Vielen Dank.','anycontact');?></strong>
				</p>
			</div>
		</noscript>
    <form id="p-ac-new-form-submit" method="post" action="#">
			<input type="hidden" id="wp_nonce" value="<?= wp_create_nonce('pac_form');?>" />
			<p><input type="submit" class="button button-primary" value="<?= _e('Speichern','anycontact');?>" /></p>
      <div class="p-ac-box p-ac-box-margin">
        <div class="p-ac-box-title"><?= _e('Name','anycontact');?></div>
        <div class="p-ac-box-content">
          <input id="p-ac-list-name" type="text" class="p-ac-full-width" name="title" value="<?= sprintf(__('Formular %d','anycontact'),$next_form_number);?>" />
        </div>
      </div>
			<div class="p-ac-box-margin p-ac-overflow p-ac-clear">
				<div class="p-ac-half p-ac-half-padding-right p-ac-left">
					<div class="p-ac-box">
						<div class="p-ac-box-title"><?= _e('Ihr Formular','anycontact');?></div>
						<div class="p-ac-box-content">
							<div id="p-ac-list-field" style="min-height: 50px;">
								<?php
								AnyContact_HTML_GetListElement(array('type' => 'select', 'title' => __('Anrede:','anycontact'), 'options' => __('Herr','anycontact').','.__('Frau','anycontact')));
								AnyContact_HTML_GetListElement(array('type' => 'text', 'title' => __('Name:','anycontact')));
								AnyContact_HTML_GetListElement(array('type' => 'text', 'title' => __('Betreff:','anycontact')));
								AnyContact_HTML_GetListElement(array('type' => 'textarea', 'title' => __('Anfrage:','anycontact')));
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="p-ac-half p-ac-half-padding-left p-ac-right">
					<div class="p-ac-box">
						<div class="p-ac-box-title"><?= _e('Formularoptionen','anycontact');?> (<?= _e('ziehen Sie diese einfach in Ihr Formular','anycontact');?>)</div>
						<div class="p-ac-box-content">
							<div id="p-ac-list-target">
								<?php
								$GetListElementArray = array(array('type' => 'text'),
																						 array('type' => 'textarea'),
																						 array('type' => 'mail', 'title' => __('Ihre E-Mail-Adresse:','anycontact')),
																						 array('type' => 'url', 'title' => __('Ihre Webseite:','anycontact')),
																						 array('type' => 'tel', 'title' => __('Ihre Telefonnummer:','anycontact')),
																						 array('type' => 'number'),
																						 array('type' => 'character'),
																						 array('type' => 'date'),
																						 array('type' => 'select'),
																						 //array('type' => 'radio'),
																						 array('type' => 'checkbox'),
																						 array('type' => 'captcha', 'title' => __('Sicherheitseingabe:','anycontact'))
																						 );
								foreach($GetListElementArray AS $array) {
									AnyContact_HTML_GetListElement($array);
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
      <div class="p-ac-box p-ac-box-margin">
        <div class="p-ac-box-title"><?= _e('Textausgabe','anycontact');?></div>
        <div id="p-ac-list-text" class="p-ac-box-content">
					
          <label for="text_success"><?= _e('Die Nachricht wurde erfolgreich gesendet.','anycontact');?></label>
          <input id="text_success" type="text" class="p-ac-full-width" name="success" value="<?= _e('Ihre Nachricht wurde erfolgreich versendet. Vielen Dank.','anycontact');?>" />
					<div class="p-ac-divider-15"></div>
					
          <label for="text_error_code"><?= _e('Die Nachricht konnte nicht gesendet werden.','anycontact');?></label>
          <input id="text_error_code" type="text" class="p-ac-full-width" name="error_code" value="<?= _e('Ihre Nachricht konnte nicht gesendet werden. Bitte versuchen Sie es später erneut oder kontaktieren Sie den Administrator dieser Webseite.','anycontact');?>" />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_spam"><?= _e('Die Nachricht wurde als Spam erkannt.','anycontact');?></label>
          <input id="text_error_spam" type="text" class="p-ac-full-width" name="error_spam" value="<?= _e('Ihre Nachricht wurde als Spam erkannt und wurde nicht weitergeleitet.','anycontact');?>" />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_form"><?= _e('Die Formulareingaben sind fehlerhaft.','anycontact');?></label>
					<input id="text_error_form" type="text" class="p-ac-full-width" name="error_form" value="<?= _e('Ihre Eingaben sind leider fehlerhaft. Bitte korrigieren Sie diese.','anycontact');?>" />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_text"><?= _e('Fehlerausgabe: Textfeld','anycontact');?></label>
          <input id="text_error_text" type="text" class="p-ac-full-width" name="error_text"  value="<?= _e('Bitte überprüfen Sie Ihre Eingabe.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_textarea"><?= _e('Fehlerausgabe: Textbereich','anycontact');?></label>
          <input id="text_error_textarea" type="text" class="p-ac-full-width" name="error_textarea"  value="<?= _e('Bitte überprüfen Sie Ihre Eingabe.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_mail"><?= _e('Fehlerausgabe: E-Mail','anycontact');?></label>
          <input id="text_error_mail" type="text" class="p-ac-full-width" name="error_mail"  value="<?= _e('Bitte überprüfen Sie Ihre E-Mail-Adresse.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_url"><?= _e('Fehlerausgabe: Webseite','anycontact');?></label>
          <input id="text_error_url" type="text" class="p-ac-full-width" name="error_url"  value="<?= _e('Bitte überprüfen Sie Ihre Webseite.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_tel"><?= _e('Fehlerausgabe: Telefonnummer','anycontact');?></label>
          <input id="text_error_tel" type="text" class="p-ac-full-width" name="error_tel"  value="<?= _e('Bitte überprüfen Sie Ihre Telefonnummer.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_number"><?= _e('Fehlerausgabe: Zahlen','anycontact');?></label>
          <input id="text_error_number" type="text" class="p-ac-full-width" name="error_number"  value="<?= _e('In diesem Feld sind nur Zahlen erlaubt.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_character"><?= _e('Fehlerausgabe: Buchstaben','anycontact');?></label>
          <input id="text_error_character" type="text" class="p-ac-full-width" name="error_character"  value="<?= _e('In diesem Feld sind nur Buchstaben erlaubt.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_date"><?= _e('Fehlerausgabe: Datum','anycontact');?></label>
          <input id="text_error_date" type="text" class="p-ac-full-width" name="error_date"  value="<?= _e('Die Angabe des Datum scheint nicht konform zu sein. Bitte überprüfen Sie Ihre Eingabe.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_select"><?= _e('Fehlerausgabe: Auswahlbox','anycontact');?></label>
          <input id="text_error_select" type="text" class="p-ac-full-width" name="error_select"  value="<?= _e('Bitte wählen Sie eine der folgenden Optionen aus.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_checkbox"><?= _e('Fehlerausgabe: Checkbox','anycontact');?></label>
          <input id="text_error_checkbox" type="text" class="p-ac-full-width" name="error_checkbox"  value="<?= _e('Um fortzufahren, stimmen Sie bitte der Bedingung zu.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
					<label for="text_error_captcha"><?= _e('Fehlerausgabe: CAPTCHA-Code','anycontact');?></label>
          <input id="text_error_captcha" type="text" class="p-ac-full-width" name="error_captcha"  value="<?= _e('Die Eingabe des CAPTCHA-Codes ist nicht korrekt. Bitte versuchen Sie es erneut.','anycontact');?>"  />
					<div class="p-ac-divider-15"></div>
					
          <label for="text_button_submit"><?= _e('Beschriftung Sendebutton','anycontact');?></label>
          <input id="text_button_submit" type="text" class="p-ac-full-width" name="button_submit"  value="<?= _e('Senden','anycontact');?>"  />
					
        </div>
      </div>
      <div class="p-ac-box p-ac-box-margin">
        <div class="p-ac-box-title"><?= _e('Einstellungen','anycontact');?></div>
        <div id="p-ac-list-setting" class="p-ac-box-content">
					<label for="setting_email"><?= _e('Empfänger E-Mail-Adresse (Alternativ wird die E-Mail-Adresse Ihres Blogs verwendet)','anycontact');?></label>
          <input id="setting_email" type="text" class="p-ac-full-width" name="email" />
					<div class="p-ac-divider-15"></div>
					
					<label for="setting_email_subject"><?= _e('Betreffzeile der Anfrage über das Kontaktformular','anycontact');?></label>
          <input id="setting_email_subject" type="text" class="p-ac-full-width" name="email_subject" value="<?= _e('Anfrage Kontaktformular','anycontact');?>" />
        </div>
      </div>
      <input type="submit" class="button button-primary" value="<?= _e('Speichern','anycontact');?>" />
    </form>
  </div>
  <?
}

function AnyContact_HTML_EditForm() {
  ?>
  <div class="wrap" id="plugin-anycontact">
    <h2><?php _e('AnyContact &raquo; Formular bearbeiten','anycontact');?></h2>
    <br />
		<div id="p-ac-placeholder-for-message"></div>
		<noscript>
			<div class="error">
				<p>
					<strong><? _e('Bitte aktivieren Sie Javascript um das Plugin nutzen zu können. Vielen Dank.','anycontact');?></strong>
				</p>
			</div>
		</noscript>
		<?php
		$post = get_post($_GET['id']);
		if($post === NULL) {
			?>
			<div class="error">
				<p>
					<strong><? _e('Das angegebene Formular wurde nicht gefunden.','anycontact');?></strong>
				</p>
			</div>
			<?
		} else {
			$post_args = json_decode($post->post_content,true);
			?>
			<form id="p-ac-edit-form-submit" method="post" action="#">
				<input type="hidden" id="wp_nonce" value="<?= wp_create_nonce('pac_form');?>" />
				<input type="hidden" id="id" value="<?= $post->ID;?>" />
				<p><input type="submit" class="button button-primary" value="<?= _e('Speichern','anycontact');?>" /></p>
				<div class="p-ac-box p-ac-box-margin">
					<div class="p-ac-box-title"><?= _e('Name','anycontact');?></div>
					<div class="p-ac-box-content">
						<input id="p-ac-list-name" type="text" class="p-ac-full-width" name="title" value="<?= $post->post_title;?>" />
					</div>
				</div>
				<div class="p-ac-box-margin p-ac-overflow p-ac-clear">
					<div class="p-ac-half p-ac-half-padding-right p-ac-left">
						<div class="p-ac-box">
							<div class="p-ac-box-title"><?= _e('Ihr Formular','anycontact');?></div>
							<div class="p-ac-box-content">
								<div id="p-ac-list-field" style="min-height: 50px;">
									<?php
									foreach($post_args['field'] AS $args) {
										AnyContact_HTML_GetListElement($args);
									}
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="p-ac-half p-ac-half-padding-left p-ac-right">
						<div class="p-ac-box">
							<div class="p-ac-box-title"><?= _e('Formularoptionen','anycontact');?> (<?= _e('ziehen Sie diese einfach in Ihr Formular','anycontact');?>)</div>
							<div class="p-ac-box-content">
								<div id="p-ac-list-target">
									<?php
									$GetListElementArray = array(array('type' => 'text'),
																							 array('type' => 'textarea'),
																							 array('type' => 'mail'),
																							 array('type' => 'url'),
																							 array('type' => 'tel'),
																							 array('type' => 'number'),
																							 array('type' => 'character'),
																							 array('type' => 'date'),
																							 array('type' => 'select'),
																							 //array('type' => 'radio'),
																							 array('type' => 'checkbox'),
																							 array('type' => 'captcha')
																							 );
									foreach($GetListElementArray AS $array) {
										AnyContact_HTML_GetListElement($array);
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="p-ac-box p-ac-box-margin">
					<div class="p-ac-box-title"><?= _e('Textausgabe','anycontact');?></div>
					<div id="p-ac-list-text" class="p-ac-box-content">
						
						<label for="text_success"><?= _e('Die Nachricht wurde erfolgreich gesendet.','anycontact');?></label>
						<input id="text_success" type="text" class="p-ac-full-width" name="success" value="<?= $post_args['text']['success'];?>" />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_code"><?= _e('Die Nachricht konnte nicht gesendet werden.','anycontact');?></label>
						<input id="text_error_code" type="text" class="p-ac-full-width" name="error_code" value="<?= $post_args['text']['error_code'];?>" />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_spam"><?= _e('Die Nachricht wurde als Spam erkannt.','anycontact');?></label>
						<input id="text_error_spam" type="text" class="p-ac-full-width" name="error_spam" value="<?= $post_args['text']['error_spam'];?>" />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_form"><?= _e('Die Formulareingaben sind fehlerhaft.','anycontact');?></label>
						<input id="text_error_form" type="text" class="p-ac-full-width" name="error_form" value="<?= $post_args['text']['error_form'];?>" />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_text"><?= _e('Fehlerausgabe: Textfeld','anycontact');?></label>
						<input id="text_error_text" type="text" class="p-ac-full-width" name="error_text"  value="<?= $post_args['text']['error_text'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_textarea"><?= _e('Fehlerausgabe: Textbereich','anycontact');?></label>
						<input id="text_error_textarea" type="text" class="p-ac-full-width" name="error_textarea"  value="<?= $post_args['text']['error_textarea'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_mail"><?= _e('Fehlerausgabe: E-Mail','anycontact');?></label>
						<input id="text_error_mail" type="text" class="p-ac-full-width" name="error_mail"  value="<?= $post_args['text']['error_mail'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_url"><?= _e('Fehlerausgabe: Webseite','anycontact');?></label>
						<input id="text_error_url" type="text" class="p-ac-full-width" name="error_url"  value="<?= $post_args['text']['error_url'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_tel"><?= _e('Fehlerausgabe: Telefonnummer','anycontact');?></label>
						<input id="text_error_tel" type="text" class="p-ac-full-width" name="error_tel"  value="<?= $post_args['text']['error_tel'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_number"><?= _e('Fehlerausgabe: Zahlen','anycontact');?></label>
						<input id="text_error_number" type="text" class="p-ac-full-width" name="error_number"  value="<?= $post_args['text']['error_number'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_character"><?= _e('Fehlerausgabe: Buchstaben','anycontact');?></label>
						<input id="text_error_character" type="text" class="p-ac-full-width" name="error_character"  value="<?= $post_args['text']['error_character'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_date"><?= _e('Fehlerausgabe: Datum','anycontact');?></label>
						<input id="text_error_date" type="text" class="p-ac-full-width" name="error_date"  value="<?= $post_args['text']['error_date'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_select"><?= _e('Fehlerausgabe: Auswahlbox','anycontact');?></label>
						<input id="text_error_select" type="text" class="p-ac-full-width" name="error_select"  value="<?= $post_args['text']['error_select'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_checkbox"><?= _e('Fehlerausgabe: Checkbox','anycontact');?></label>
						<input id="text_error_checkbox" type="text" class="p-ac-full-width" name="error_checkbox"  value="<?= $post_args['text']['error_checkbox'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_error_captcha"><?= _e('Fehlerausgabe: CAPTCHA-Code','anycontact');?></label>
						<input id="text_error_captcha" type="text" class="p-ac-full-width" name="error_captcha"  value="<?= $post_args['text']['error_captcha'];?>"  />
						<div class="p-ac-divider-15"></div>
						
						<label for="text_button_submit"><?= _e('Beschriftung Sendebutton','anycontact');?></label>
						<input id="text_button_submit" type="text" class="p-ac-full-width" name="button_submit"  value="<?= $post_args['text']['button_submit'];?>"  />
						
					</div>
				</div>
				<div class="p-ac-box p-ac-box-margin">
					<div class="p-ac-box-title"><?= _e('Einstellungen','anycontact');?></div>
					<div id="p-ac-list-setting" class="p-ac-box-content">
						<label for="setting_email"><?= _e('E-Mail-Adresse (Alternativ wird die E-Mail-Adresse Ihres Blogs verwendet)','anycontact');?></label>
						<input id="setting_email" type="text" class="p-ac-full-width" name="email" value="<?= $post_args['setting']['email'];?>" />
						<div class="p-ac-divider-15"></div>
					
					<label for="setting_email_subject"><?= _e('Betreffzeile der Anfrage über das Kontaktformular','anycontact');?></label>
          <input id="setting_email_subject" type="text" class="p-ac-full-width" name="email_subject" value="<?= $post_args['setting']['email_subject'];?>" />
					</div>
				</div>
				<input type="submit" class="button button-primary" value="<?= _e('Speichern','anycontact');?>" />
			</form>
			<?
		}
		?>
		</div>
	<?
}

function AnyContact_HTML_GetListElement($args) {
	?>
	<div class="p-ac-list-element">
		<div class="p-ac-list-element-header">
			<?php
			switch($args['type']) {
				case 'captcha':		_e('Eingabefeld: Captcha','anycontact'); break;
				case 'character':	_e('Eingabefeld: Buchstaben','anycontact'); break;
				case 'checkbox':	_e('Checkbox','anycontact'); break;
				case 'date':			_e('Eingabefeld: Datum','anycontact'); break;
				case 'mail':			_e('Eingabefeld: E-Mail','anycontact'); break;
				case 'number':		_e('Eingabefeld: Zahlen','anycontact'); break;
				case 'select':		_e('Auswahlbox','anycontact'); break;
				case 'tel':				_e('Eingabefeld: Telefonnummer','anycontact'); break;
				case 'text':			_e('Eingabefeld','anycontact'); break;
				case 'textarea':	_e('Eingabebereich','anycontact'); break;
				case 'url':				_e('Eingabefeld: Webseite','anycontact'); break;
			}
			?>
		</div>
		<div class="p-ac-list-element-content">
			<input type="hidden" name="type" value="<?= $args['type'];?>" />
			<p><?php _e('Titel','anycontact');?></p>
			<p><input type="text" class="regular-text" name="title" value="<?= $args['title'];?>" /></p>
			<br />
			<?
			
			if(in_array($args['type'],array('character','number','text','textarea'))) {
				?>
				<table>
					<tr>
						<td><?php _e('Mindestlänge','anycontact');?></td>
						<td><?php _e('Maximallänge','anycontact');?></td>
					</tr>
					<tr>
						<td><input type="text" class="smal-text" name="length_min" value="<?= $args['length_min'];?>" /></td>
						<td><input type="text" class="smal-text" name="length_max" value="<?= $args['length_max'];?>" /></td>
					</tr>
				</table>
				<br />
				<?
			}
			
			if(in_array($args['type'],array('select'))){
				?>
				<p><?php _e('Optionen (mit Komma getrennt)','anycontact');?></p>
				<p><textarea name="options"><?= $args['options'];?></textarea></p>
				<br />
				<?
			}
			
			if($args['type'] == 'checkbox'){
				?>
				<p><?php _e('Link-Titel','anycontact');?></p>
				<p><input type="text" class="smal-text" name="link_title" value="<?= $args['link_title'];?>" /></p>
				<br />
				<p><?php _e('Link-URL','anycontact');?></p>
				<p><input type="text" class="smal-text" name="link_url" value="<?= $args['link_url'];?>" /></p>
				<br />
				<?
			}
			
			if(!in_array($args['type'],array('captcha','select'))) {
				?>
				<p><?php _e('Pflichtfeld','anycontact');?></p>
				<p><input type="checkbox" name="required" <?= ($args['required'] == 'true')?'checked':false;?> /></p>
				<br />
				<?
			}
			
			?>
			<p><a href="#" onclick="jQuery(this).parents('.p-ac-list-element').remove();"><?php _e('Löschen','anycontact');?></a></p>
		</div>
	</div>
	<?
}

?>