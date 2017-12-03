<?php

if(is_logged()){
	set_error('Erreur 404', 'zoom-out', 'La page à laquelle vous tentez d\'accéder n\'existe pas', 'home');
}else{
	set_error('Erreur 404', 'zoom-out', 'La page à laquelle vous tentez d\'accéder n\'existe pas', 'login');
}
