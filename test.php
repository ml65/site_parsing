<?php

$str = "
592ec3187ede46'],['10','11','d7c6537818f04cf7bcf174467d0c31cf'],['22','4a142e8737f447a798a73f277af06743']],true);</script><script type=\"text/javascript\">FE.add('onready',function(e,t){ try{ initAnchors();
;window.initFileFields && initFileFields(10485760,1,10485760);; }catch(err){alert(err);} });</script>
<script type=\"text/javascript\">if (window.gtag) {gtag('get', '', 'client_id', function(clientId) {window.ga_cid = clientId;console.log('got client id: ', 
window.ga_cid);$('.frm_field1').val(window.ga_cid);});} else if (window.ga){ga(function(t){window.ga_cid = t.get('clientId');console.log('got client id: ', 
window.ga_cid);$('.frm_field1').val(window.ga_cid);});}</script></body>
</html>

 Санкт-Петербург asdasdf 

";
$mask = '/санкт-петербург|спб|питер|st\.?\s*petersburg/ui';

if (preg_match($mask, $str)) {
    echo "!!!!!!!!!!!!!!!!!!";
} else {
    echo "------------------";
}