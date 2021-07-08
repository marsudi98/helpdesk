<script src="<?= BASE_URL;?>js/datepicker.js" type="text/javascript"></script>
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
  var encodedHeader     = base64url(stringifiedHeader);
<?php 
  if (JAK_SUPERADMINACCESS) {
    $dashboardnya = 1;
  } elseif ($jakuser->getVar('is_dp')) {
    $dashboardnya = 34;
    $paramnya     = 'dp';
    $param_val    = $jakuser->getVar('username');
  } else {
    $dashboardnya = 33;
    $paramnya     = 'operator_id';
    $param_val    = $jakuser->getVar('id');
  }
?>
  var payload = {
    resource: { dashboard: <?= $dashboardnya ?> },
    params: {<?php if (JAK_SUPERADMINACCESS == false || JAK_SUPERADMINACCESS == 0) { ?> <?= $paramnya ?> : '<?=$param_val?>' <?php } ?>},
    exp: Math.round(Date.now() / 1000) + (60 * 60) // 60 minute expiration
  };
  var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(payload));
  var encodedData = base64url(stringifiedData);

  var token = encodedHeader + "." + encodedData;

  var METABASE_SITE_URL = "http://10.20.20.117:3000";
  var METABASE_SECRET_KEY = "5942a4c3a2422fbe87da57f7a8d205258b05f07c60078495c0e17d58e2f74e6e";

  var signature = CryptoJS.HmacSHA256(token, METABASE_SECRET_KEY);
  signature = base64url(signature);

  var signedToken = token + "." + signature;

  var link_metabase = METABASE_SITE_URL + "/embed/dashboard/" + signedToken + "#bordered=true&titled=true";
  document.getElementById("iframe_metabase").setAttribute("src",link_metabase);
</script>

<script type="text/javascript">
  $(document).ready(function() {
    // document.getElementById('report_date').value = new Date("YYYY-MM-DD");
    $('#report_date').datetimepicker({
      // format: '',
      format: 'YYYY-MM-DD',
      icons: {
        time: "fa fa-clock",
        date: "fa fa-calendar",
        up: "fa fa-chevron-up",
        down: "fa fa-chevron-down",
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove'
      }
    });
  });
  
  function go_to_download_page(){
    report_date_nya = document.getElementById("report_date").value;
    if (report_date_nya != "" && report_date_nya != null) {
      link = "<?= BASE_URL_ORIG ?>operator/index.php?p=custom_dashboard&sp=operator&report_date="+report_date_nya;
      window.location = link;
    } else {
    	alert("Tanggal report kosong!");
    }
  }
</script>