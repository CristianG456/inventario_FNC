<?php
// create a fake upload
\ = curl_init('http://nginx_server/equipos/importar');
curl_setopt(\, CURLOPT_POST, 1);
\ = new CURLFile('/var/www/storage/app/public/dummy.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'dummy.xlsx');
curl_setopt(\, CURLOPT_POSTFIELDS, ['archivo' => \]);
curl_setopt(\, CURLOPT_RETURNTRANSFER, true);
// get headers too
curl_setopt(\, CURLOPT_HEADER, true);
\ = curl_exec(\);
\ = curl_getinfo(\, CURLINFO_HTTP_CODE);
if(curl_errno(\)){
    echo 'Curl error: ' . curl_error(\);
}
echo " HTTP Code: \\n\;
echo