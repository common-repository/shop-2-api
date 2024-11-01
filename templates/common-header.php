<?php
require_once SHOP2API_PLUGIN_PATH . '/includes/Base/CommonFunctions.php';
$allowed_html = Shop2API_CommonFunctions::expanded_alowed_tags();
?>
<div id='common-success' class='success-message'></div>
<div id='common-error' class='error-message'></div>

<div class="common-header">
    <img class='shop2api-logo' src='<?php echo(SHOP2API_PLUGIN_URL . "/assets/logo.png") ?>' alt="logo"/>
    <div class="heading-icons">
        <div class="tooltip">
            <a href="https://www.shop2api.com/index.php/faqs/" target="_blank">
                <div class="tooltiptext">FAQ</div>
                <span class="material-icons-outlined">help_center</span>
            </a>
        </div>
        <div class="tooltip">
            <a href="https://wordpress.org/support/plugin/shop-2-api/" target="_blank">
                <div class="tooltiptext">Log a Bug or a Support question</div>
                <span class="material-icons-outlined">bug_report</span>
            </a>
        </div>
    </div>
</div>
<div>
    <h2>
        <?php if (isset($shop2api_header)) {
            echo wp_kses($shop2api_header, $allowed_html);
        } ?>
    </h2>
    <?php if (isset($shop2api_header_detail)) {
        echo wp_kses($shop2api_header_detail, $allowed_html);
    } ?>
</div>
