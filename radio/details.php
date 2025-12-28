<?php

define('APP_NAME', 'QR Codes');
define('PAGE_TITLE', 'Dashboard');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

$query = 'SELECT *
    FROM events
    WHERE id = "'.$_GET['key'].'"
    LIMIT 1';
$result = mysqli_query($connect, $query);
$record = mysqli_fetch_assoc($result);

?>


<div class="w3-center">
    <h1><?=$record['name']?></h1>
</div>

<hr>

<?php if($record['banner']): ?>
    <img src="<?=$record['banner']?>" class="w3-image" style="width: 100%;">
<?php endif; ?>

<p><?=nl2br($record['description'])?></p>

<?php if(!$record['registration']): ?>
    <p>Note: <span class="w3-bold">No registration required, just show up!</span></p>
<?php endif; ?>

<hr>

<p>
    Date: <span class="w3-bold"><?=date_to_format($record['starts_at'], 'FULL')?></span>
    <br>
    Location: <span class="w3-bold"><?=$record['location']?></span>
</p>

<hr>

<?php if($record['registration']): ?>
    <a href="<?=$record['registration']?>" class="w3-button w3-white w3-border">
        <i class="fa-solid fa-pen fa-padding-right"></i> Register
    </a>
    <hr>
<?php endif; ?>

<?php if($record['online']): ?>
    <a href="<?=$record['online']?>" class="w3-button w3-white w3-border">
        <i class="fa-solid fa-globe fa-padding-right"></i> Join Online
    </a>
<?php endif; ?>
<a href="<?=ENV_DOMAIN?>/list" class="w3-button w3-white w3-border">
    <i class="fa-solid fa-caret-left fa-padding-right"></i>
    Back to Event List
</a>
<a href="<?=ENV_DOMAIN?>/calendar" class="w3-button w3-white w3-border">
    <i class="fa-solid fa-calendar fa-padding-right"></i>
    Back to Calendar View
</a>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');