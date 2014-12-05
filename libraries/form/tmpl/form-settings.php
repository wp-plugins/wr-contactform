<?php
if ( ! empty( $_POST[ 'wr_contactform_config' ] ) ) {
	foreach ( $_POST[ 'wr_contactform_config' ] as $key => $value ) {
		if ( get_option( $key ) !== false ) {
			// The option already exists, so we just update it.
			update_option( $key, $value );
		}
		else {
			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$deprecated = null;
			$autoload = 'no';
			add_option( $key, $value, $deprecated, $autoload );
		}
	}
}
$loadBootstrapCss = get_option( 'wr_contactform_load_bootstrap_css', 1 );
$checkLoadCssBootstrap = '';
if ( $loadBootstrapCss != '0' && $loadBootstrapCss != 0 ) {
	$checkLoadCssBootstrap = 'checked="checked" ';
}
$loadBootstrapJs = get_option( 'wr_contactform_load_bootstrap_js', 1 );

$checkLoadJsBootstrap = '';
if ( $loadBootstrapJs != '0' && $loadBootstrapJs != 0 ) {
	$checkLoadJsBootstrap = 'checked="checked" ';
}
$envato_username = '';
$envato_api_key = '';
$envato_purchase_code = '';
$customer_account = get_option( 'wr_cf_customer_account', null );

if ( ! empty( $customer_account ) ) {
	$envato_username = $customer_account['envato_username'];
	$envato_api_key = $customer_account['envato_api_key'];
	$envato_purchase_code = $customer_account['envato_purchase_code'];
	
	$settings['envato_username'] = $envato_username;
	$settings['envato_api_key'] = $envato_api_key;
	$settings['envato_purchase_code'] = $envato_purchase_code;
	do_action( 'wr_envato_purchase_data', WR_CONTACTFORM_IDENTIFICATION, $settings );
}
?>
<div class="wrap">
	<h2><?php echo '' . __( 'WR ContactForm Settings', WR_CONTACTFORM_TEXTDOMAIN );?></h2>
	<?php if ( ! empty( $_POST ) ) { ?>
	<div class="updated below-h2" id="message"><p>Settings updated.</p></div>
	<?php } ?>
	<form method="POST" id="wr_contactform_settings">
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row">
					<label><?php echo '' . __( 'Load Bootstrap Assets', WR_CONTACTFORM_TEXTDOMAIN );?></label>
				</th>
				<td>
					<label class="auto-get-data">
						<input type="checkbox" <?php echo '' . $checkLoadJsBootstrap;?> value="1" id="wr_contactform_load_bootstrap_js"> <?php echo '' . __( 'JS', WR_CONTACTFORM_TEXTDOMAIN );?>
						<input type="hidden" value="<?php echo '' . $loadBootstrapJs;?>" name="wr_contactform_config[wr_contactform_load_bootstrap_js]" id="wr_confwr_contactform_load_bootstrap_js" />
					</label>
					<br>
					<label class="auto-get-data">
						<input type="checkbox" <?php echo '' . $checkLoadCssBootstrap;?> value="1" id="wr_contactform_load_bootstrap_css"> <?php echo '' . __( 'CSS', WR_CONTACTFORM_TEXTDOMAIN );?>
						<input type="hidden" value="<?php echo '' . $loadBootstrapCss;?>" name="wr_contactform_config[wr_contactform_load_bootstrap_css]" id="wr_confwr_contactform_load_bootstrap_css" />
					</label>

					<p class="description"><?php echo '' . __( 'You should choose NOT to load Bootstrap JS / CSS if your theme or some other plugin installed on your website already loaded it.', WR_CONTACTFORM_TEXTDOMAIN );?></p>
				</td>
			</tr>
			<?php do_action( 'wr_contactform_action_config' );?>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
	</form>
	<div class="wr-banner-wrapper">
		<h3>See our other free awesomeness</h3>
		<a class="wr-banner" href="http://www.woorockets.com/plugins/wr-megamenu/?utm_source=ContactForm%20Setting&utm_medium=banner&utm_campaign=Cross%20Promo%20Plugins" target="_blank">
			<img width="278" height="156" src="<?php echo WR_CONTACTFORM_URI . 'assets/images/banners/MegaMenu_S.jpg'; ?>" alt="WR Mega Menu" />
		</a>
		<a class="wr-banner" href="http://www.woorockets.com/plugins/wr-pagebuilder/?utm_source=ContactForm%20Setting&utm_medium=banner&utm_campaign=Cross%20Promo%20Plugins" target="_blank">
			<img width="278" height="156" src="<?php echo WR_CONTACTFORM_URI . 'assets/images/banners/PageBuilder_S.jpg'; ?>" alt="WR Page Builder" />
		</a>
		<a class="wr-banner" href="http://www.woorockets.com/themes/corsa/?utm_source=ContactForm%20Setting&utm_medium=banner&utm_campaign=Cross%20Promo%20Plugins" target="_blank">
			<img width="278" height="156" src="<?php echo WR_CONTACTFORM_URI . 'assets/images/banners/Corsa_S.jpg'; ?>" alt="WR Corsa" />
		</a>
	</div>
</div>
<?php
$script = '(function ($) {
	$(".jsn-modal-overlay,.jsn-modal-indicator").remove();
    $("body").append($("<div/>", {
        "class":"jsn-modal-overlay",
        "style":"z-index: 1000; display: inline;"
    })).append($("<div/>", {
        "class":"jsn-modal-indicator",
        "style":"display:block"
    })).addClass("jsn-loading-page");
    $("#wr_contactform_settings label.auto-get-data input:checkbox").change(function(){
		if($(this).is(":checked")){
			$(this).parent().find("input:hidden").val(1);
		}else{
			$(this).parent().find("input:hidden").val(0);
		}
    });
     setTimeout(function () {
        $("#wpbody-content").show();
        $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
   }, 500);
  })(jQuery);';
WR_CF_Init_Assets::inline( 'js', $script );

$style = '
	.wr-banner-wrapper .wr-banner { float: left; line-height: 0; margin: 0px 10px 0px 10px; }
';
WR_CF_Init_Assets::inline( 'css', $style );
