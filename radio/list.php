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
    WHERE starts_at >= CURDATE()
    ORDER BY starts_at ASC
    LIMIT 4';
$result = mysqli_query($connect, $query);

?>

<main>
    
    <div class="w3-center">
        <h1>Upcoming Events</h1>
        <a href="<?=ENV_DOMAIN?>/list">Upcoming Events</a> | <a href="<?=ENV_DOMAIN?>/calendar">Calendar View</a>
    </div>

    <hr>

    <div class="w3-flex" style="flex-wrap: wrap; gap: 16px; align-items: stretch;">

        <?php while ($record = mysqli_fetch_assoc($result)): ?>

            <div style="width: calc(50% - 16px); box-sizing: border-box; display: flex; flex-direction: column;">
                <div class="w3-card-4 w3-margin-top" style="max-width:100%; height: 100%; display: flex; flex-direction: column;">

                    <header class="w3-container w3-purple">
                        <h4 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?=$record['name']?></h4>
                    </header>

                    <div class="w3-margin">
                        <a href="<?=ENV_DOMAIN?>/details/<?=$record['id']?>">
                            <?php if($record['thumbnail']): ?>
                                <img src="<?=$record['thumbnail']?>" class="w3-image" style="width: 100%; ">
                            <?php else: ?>
                                <img src="https://cdn.brickmmo.com/images@1.0.0/no-calendar.png" class="w3-image" style="width: 100%;">
                            <?php endif; ?>
                        </a>
                        
                        <div class="w3-margin-top" style="flex: 1;">
                            Date: <span class="w3-bold"><?=date_to_format($record['starts_at'], 'SHORT_FULL')?></span>
                            <br>
                            Location: <span class="w3-bold"><?=$record['location']?></span>
                        </div>
                        
                        <div class="w3-margin-top">
                            <a href="<?=ENV_DOMAIN?>/details/<?=$record['id']?>" class="w3-button w3-white w3-border w3-block">Event Details</a>
                        </div>
                    </div>

                </div>
            </div>

        <?php endwhile; ?>

    </div>

</main>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');