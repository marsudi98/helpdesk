<?php
    $link_metabase = "http://10.100.80.28:3000/public/dashboard/c567641d-c47c-4726-858e-9af9b549b895";
?>
<script src="<?php echo BASE_URL;?>js/datepicker.js" type="text/javascript"></script>
<?php if(JAK_SUPERADMINACCESS != 1) { ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js" integrity="sha512-nOQuvD9nKirvxDdvQ9OMqe2dgapbPB7vYAMrzJihw5m+aNcf0dX53m6YxM4LgA9u8e9eg9QX+/+mPu8kCNpV2A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
  function base64url(source) {
    // Encode in classical base64
    encodedSource = CryptoJS.enc.Base64.stringify(source);

    // Remove padding equal characters
    encodedSource = encodedSource.replace(/=+$/, '');

    // Replace characters according to base64url specifications
    encodedSource = encodedSource.replace(/\+/g, '-');
    encodedSource = encodedSource.replace(/\//g, '_');

    return encodedSource;
  }

  var header = {
  "alg": "HS256",
  "typ": "JWT"
  };

  var stringifiedHeader = CryptoJS.enc.Utf8.parse(JSON.stringify(header));
  var encodedHeader = base64url(stringifiedHeader);
<?php if ($jakuser->getVar('is_dp')) { ?>
  var payload = {
    resource: { dashboard: 34 },
    params: {dp : "<?=$jakuser->getVar('username')?>"},
    exp: Math.round(Date.now() / 1000) + (60 * 60) // 60 minute expiration
  };
<?php } else { ?>
  var payload = {
    resource: { dashboard: 33 },
    params: {operator_id : <?=$jakuser->getVar('id')?>},
    exp: Math.round(Date.now() / 1000) + (60 * 60) // 60 minute expiration
  };
<?php } ?>
  var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(payload));
  var encodedData = base64url(stringifiedData);

  var token = encodedHeader + "." + encodedData;

  var METABASE_SITE_URL = "http://10.100.80.28:3000";
  var METABASE_SECRET_KEY = "5942a4c3a2422fbe87da57f7a8d205258b05f07c60078495c0e17d58e2f74e6e";

  var signature = CryptoJS.HmacSHA256(token, METABASE_SECRET_KEY);
  signature = base64url(signature);

  var signedToken = token + "." + signature;

  var link_metabase = METABASE_SITE_URL + "/embed/dashboard/" + signedToken + "#theme=night&bordered=true&titled=true";
  document.getElementById("iframe_metabase").setAttribute("src",link_metabase);
</script>
<?php } else { ?>
<script type="text/javascript">
  document.getElementById("iframe_metabase").setAttribute("src","<?= $link_metabase ?>");
</script>
<?php } ?>