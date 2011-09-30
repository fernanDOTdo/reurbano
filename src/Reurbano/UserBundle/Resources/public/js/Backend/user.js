jQuery(document).ready(function($) {
    $('a[rel*=facebox]').facebox()
    jQuery.facebox({ ajax: 'remote.html' });
    jQuery.facebox({ ajax: 'remote.html' }, 'my-groovy-style'); 
}) 