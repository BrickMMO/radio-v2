<?php

security_check();
admin_check();

if (isset($_GET['delete'])) 
{

    $query = 'DELETE FROM events 
        WHERE id = '.$_GET['delete'].'
        LIMIT 1';
    mysqli_query($connect, $query);

    message_set('Delete Success', 'Event has been deleted.');
    header_redirect('/admin/dashboard');
    
}
elseif (isset($_GET['copy'])) 
{

    $query = 'INSERT INTO events (
            name,
            description,
            location,
            registration, 
            online, 
            thumbnail,
            banner,
            starts_at,
            ends_at,
            created_at,
            updated_at,
            deleted_at
        )
        SELECT CONCAT("Copy of ",name),
            description,
            location,
            registration, 
            online, 
            thumbnail,
            banner,
            starts_at,
            ends_at,
            NOW(),
            NOW(),
            NULL
        FROM events
        WHERE id = '.$_GET['copy'];
    mysqli_query($connect, $query);

    message_set('Copy Success', 'Event has been copied.');
    header_redirect('/admin/dashboard');
    
}

define('APP_NAME', 'Colours');
define('PAGE_TITLE', 'Dashboard');
define('PAGE_SELECTED_SECTION', 'admin-dashboard');
define('PAGE_SELECTED_SUB_PAGE', '/admin/dashboard');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');    

$query = 'SELECT * 
    FROM events
    ORDER BY starts_at DESC';    
$result = mysqli_query($connect, $query);

$events_count = mysqli_num_rows($result);

?>

<!-- CONTENT -->

<h1 class="w3-margin-top w3-margin-bottom">
    <img
        src="https://cdn.brickmmo.com/icons@1.0.0/events.png"
        height="50"
        style="vertical-align: top"
    />
    Events
</h1>

<p>
    Number of events: <span class="w3-tag w3-blue"><?=$events_count?></span>    
</p>

<hr />

<h2>Event List</h2>

<table class="w3-table w3-bordered w3-striped w3-margin-bottom">
    <tr>
        <th class="bm-table-icon"></th>
        <th class="bm-table-icon"></th>
        <th>Name</th>
        <th class="bm-table-icon"></th>
        <th class="bm-table-icon"></th>
        <th class="bm-table-icon"></th>
        <th class="bm-table-icon"></th>
        <th class="bm-table-icon"></th>
    </tr>

    <?php while ($record = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td>
                <?php if($record['thumbnail']): ?>
                    <a href="<?=ENV_DOMAIN?>/admin/thumbnail/<?=$record['id'] ?>">
                        <img src="<?=$record['thumbnail']?>" width="70">
                    </a>
                <?php endif; ?>
            </td>
            <td>
                <?php if($record['banner']): ?>
                    <a href="<?=ENV_DOMAIN?>/admin/banner/<?=$record['id'] ?>">
                        <img src="<?=$record['banner']?>" width="70">
                    </a>
                <?php endif; ?>
            </td>
            <td>
                <?=$record['name'] ?>
                <br>
                <small>
                    Location: <?=$record['location']?>
                    <br>
                    Date: <?=date_to_format($record['starts_at'], 'SHORT')?>
                    <?php if($record['registration']): ?>
                        <br>
                        Registration: <a href="<?=$record['registration']?>"><?=string_shorten($record['registration'], 50)?></a>
                    <?php endif; ?>
                    <?php if($record['online']): ?>
                        <br>
                        Online: <a href="<?=$record['online']?>"><?=string_shorten($record['online'], 50)?></a>
                    <?php endif; ?>
                </small>
            </td>
            <td>
                <a href="<?=ENV_DOMAIN?>/admin/thumbnail/<?=$record['id'] ?>">
                    <i class="fa-solid fa-image"></i>
                </a>
            </td>
            <td>
                <a href="<?=ENV_DOMAIN?>/admin/banner/<?=$record['id'] ?>">
                    <i class="fa-solid fa-panorama"></i>
                </a>
            </td>
            <td>
                <a href="#" onclick="return confirmModal('Are you sure you want to copy the event <?=$record['name'] ?>?', '/admin/dashboard/copy/<?=$record['id'] ?>');">
                    <i class="fa-solid fa-copy"></i>
                </a>
            </td>
            <td>
                <a href="<?=ENV_DOMAIN?>/admin/edit/<?=$record['id'] ?>">
                    <i class="fa-solid fa-pencil"></i>
                </a>
            </td>
            <td>
                <a href="#" onclick="return confirmModal('Are you sure you want to delete the event <?=$record['name'] ?>?', '/admin/dashboard/delete/<?=$record['id'] ?>');">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

<a
    href="<?=ENV_DOMAIN?>/admin/add"
    class="w3-button w3-white w3-border"
>
    <i class="fa-solid fa-pen-to-square fa-padding-right"></i> Add Event
</a>


<!--
<a
    href="<?=ENV_DOMAIN?>/admin/import"
    class="w3-button w3-white w3-border"
>
    <i class="fa-solid fa-download"></i> Import Colours
</a>

<hr />

<div
    class="w3-row-padding"
    style="margin-left: -16px; margin-right: -16px"
>
    <div class="w3-half">
        <div class="w3-card">
            <header class="w3-container w3-grey w3-padding w3-text-white">
                <i class="bm-colours"></i> Uptime Status
            </header>
            <div class="w3-container w3-padding">Uptime Status Summary</div>
            <footer class="w3-container w3-border-top w3-padding">
                <a
                    href="<?=ENV_DOMAIN?>/admin/uptime/colours"
                    class="w3-button w3-border w3-white"
                >
                    <i class="fa-regular fa-file-lines fa-padding-right"></i>
                    Full Report
                </a>
            </footer>
        </div>
    </div>
    <div class="w3-half">
        <div class="w3-card">
            <header class="w3-container w3-grey w3-padding w3-text-white">
                <i class="bm-colours"></i> Stat Summary
            </header>
            <div class="w3-container w3-padding">App Statistics Summary</div>
            <footer class="w3-container w3-border-top w3-padding">
                <a
                    href="<?=ENV_DOMAIN?>/stats/colours"
                    class="w3-button w3-border w3-white"
                >
                    <i class="fa-regular fa-chart-bar fa-padding-right"></i> Full Report
                </a>
            </footer>
        </div>
    </div>
</div>
-->

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');
