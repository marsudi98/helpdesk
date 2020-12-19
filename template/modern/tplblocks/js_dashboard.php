<?php if (JAK_BILLING_MODE != 0) { ?>
<script src="https://checkout.stripe.com/checkout.js"></script>
<?php } ?>
<script src="<?php echo BASE_URL;?>js/database.table.js"></script>
<script type="text/javascript">

$('#sortable-data').DataTable({"columnDefs": [
      { "orderable": false, "targets": [5] }
      ]<?php if ($BT_LANGUAGE != "en") { ?>
        , "language": {
                "url": "<?php echo BASE_URL;?>js/dt_lang/<?php echo $BT_LANGUAGE;?>.js"
        }
        <?php } ?>});

<?php if (JAK_BILLING_MODE != 0) { ?>
$('.stripe').on('click', function(e) {

    $(this).find(".jak-loadbtn").addClass("fa fa-spinner fa-spin");

    var amount = $(this).data("amount");
    var currency = $(this).data("currency");
    var package = $(this).data("package");
    var ptitle = $(this).data("title");
    var pdesc = $(this).data("description");
    var _this = $(this);

    var stripe_amount = amount*100;

    e.preventDefault();
    var handler = StripeCheckout.configure({
        key: '<?php echo JAK_STRIPE_PUBLISH_KEY;?>',
        image: 'operator/payment/img/stripe_logo.png',
        locale: 'auto',
        token: function(token) {
            // You can access the token ID with `token.id`.
            // Get the token ID to your server-side code for use.
            $("#stripeToken").val(token.id);
            $("#stripeEmail").val(token.email);
            var utok = $("input#stripeToken").val();
            var uemail = $("input#stripeEmail").val();

            if (!utok){ 
                return false; 
            } else {
                                        
                $.ajax({
                    url: "<?php echo $_SERVER['REQUEST_URI'];?>",
                    type: "POST",
                    data: "action=payment&paidhow=stripe&token="+utok+"&email="+uemail+"&amount="+amount+"&currency="+currency+"&package="+package+"&ptitle="+ptitle,
                    dataType: "json",
                    cache: false
                }).done(function(data) {

                    if (data.status == 1) {
                        $.notify({message: data.infomsg}, {type:'success'});
                    } else {
                        $.notify({message: data.infomsg}, {type:'danger'});
                    }

                });

            }
        }
    });

    // Open Checkout with further options:
    handler.open({
        name: ptitle,
        description: pdesc,
        email: '<?php echo $jakclient->getVar("email");?>',
        amount: parseInt(stripe_amount),
        currency: currency,
        closed: function () {
            $.notify({message: '<?php echo stripcslashes($jkl['hd111']);?>'}, {type:'danger'});
            $(_this).find(".jak-loadbtn").removeClass("fa fa-spinner fa-spin");
        }
    });
});

$('.paypal').on('click', function(e) {

    e.preventDefault();

    $(this).find(".jak-loadbtn").addClass("fa fa-spinner fa-spin");

    var amount = $(this).data("amount");
    var currency = $(this).data("currency");
    var package = $(this).data("package");
    var ptitle = $(this).data("title");
    var _this = $(this);

    $.ajax({
        url: "<?php echo $_SERVER['REQUEST_URI'];?>",
        type: "POST",
        data: "action=payment&paidhow=paypal&amount="+amount+"&currency="+currency+"&package="+package+"&ptitle="+ptitle,
        dataType: "json",
        cache: false
    }).done(function(data) {
                                                
        if (data.status == 1) {
            $("#paypal_form").html(data.content);
            $('#gateway_form').submit();
        } else {
            $(_this).find(".jak-loadbtn").removeClass("fa fa-spinner fa-spin");
            $.notify({message: data.infomsg}, {type:'danger'});
        }
    });

});

$('.twoco').on('click', function(e) {

    e.preventDefault();

    $(this).find(".jak-loadbtn").addClass("fa fa-spinner fa-spin");

    var amount = $(this).data("amount");
    var currency = $(this).data("currency");
    var package = $(this).data("package");
    var ptitle = $(this).data("title");
    var _this = $(this);

    $.ajax({
        url: "<?php echo $_SERVER['REQUEST_URI'];?>",
        type: "POST",
        data: "action=payment&paidhow=twoco&amount="+amount+"&currency="+currency+"&package="+package+"&ptitle="+ptitle,
        dataType: "json",
        cache: false
    }).done(function(data) {
                                                
        if (data.status == 1) {
            $("#paypal_form").html(data.content);
            $('#gateway_form').submit();
        } else {
            $(_this).find(".jak-loadbtn").removeClass("fa fa-spinner fa-spin");
            $.notify({message: data.infomsg}, {type:'danger'});
        }
    });

});
<?php } ?>
</script>