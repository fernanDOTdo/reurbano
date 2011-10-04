jQuery(document).ready(function($) {
    $('.facebox').facebox()
    jQuery.facebox({ ajax: 'remote.html' });
    jQuery.facebox({ ajax: 'remote.html' }, 'my-groovy-style'); 
}) 