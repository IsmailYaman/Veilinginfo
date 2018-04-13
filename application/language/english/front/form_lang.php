<?php

//page titles
$lang['page_title_linkform']		= 'Link request';
$lang['page_title_pageform']		= 'Page request';
$lang['page_title_contactform']		= 'Contact';

//info messages
$lang['msg_pageform_info']			= '<strong>Page request</strong> Fill in the form below completely.';
$lang['msg_linkform_info']			= '<strong>Link request</strong> With the form below you can request a link.';
$lang['msg_contactform_info']		= '<strong>Contact us</strong> Through this form you can get in touch with the administrator of %s';
$lang['msg_success']				= '<strong>Success!</strong> Request send. We will contact you as soon as possible.';
$lang['msg_success_mail']			= '<strong>Success!</strong> Message send. We will contact you as soon as possible.';

//Link Form
$lang['entry_firstname_text']		= 'Firstname';
$lang['entry_lastname_text']		= 'Lastname';
$lang['entry_email_text']			= 'E-mail';
$lang['entry_link_text']			= 'Link';
$lang['entry_backlink_text']		= 'Backlink';
$lang['entry_page_text']			= 'Page';
$lang['entry_category_text']		= 'Category';
$lang['entry_message_text']			= 'Message';
$lang['entry_anchor_text']			= 'Anchor';
$lang['entry_page_request_text']	= 'Desired Page <i><small>(<strong>mypage</strong>.%s)</small></i>';
$lang['entry_captcha_text']			= 'Anti-Spam';

//input placeholders
$lang['input_page_request_text']	= 'mypage';
$lang['input_link_text'] 			= "http://";
$lang['input_backlink_text'] 		= "http://";
$lang['input_message_text']			= "Optional";
$lang['input_page_text'] 			= "Select a page";
$lang['input_category_text']		= 'Select a page first';

//buttons
$lang['btn_request_text'] 			= "Send Request";
$lang['btn_send_text'] 				= "Send Message";

//text strings
$lang['text_no_insert']				= 'Error.';

//email
$lang['email_subject_contact']		= 'Message via contact form on %s';

//errors
$lang['error_firstname']			= 'Firstname must contain between 1 and 255 characters';
$lang['error_lastname']				= 'Lastname must contain between 1 and 255 characters';
$lang['error_page']					= 'The selected page is not valid.';
$lang['error_page_empty']			= 'Enter the name of the desired page.';
$lang['error_page_format']			= 'The desired page name must contain only letters and numbers.';
$lang['error_page_exist']			= 'The requested page exists unfortunately, pick another name and try again.';
$lang['error_category']				= 'The selected category is not valid.';
$lang['error_anchor_isset']			= 'The anchor text should contain at least 1 character';
$lang['error_anchor']				= 'The anchor text is too long. Max. 255 characters.';
$lang['error_url']					= 'URL must begin with http or https.';
$lang['error_backlink']				= 'Backlink must begin with http or https.';
$lang['error_email']				= 'Email is not valid.';
$lang['error_message']				= 'The message must contain at least one character, but no more than 1000 characters.';
$lang['error_captcha']				= 'Anti-spam invalid, try again.';

?>