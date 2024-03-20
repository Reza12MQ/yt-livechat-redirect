<?php
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YT Live Chat Redirect</title>
</head>
<body style="font-family: 'Courier New', Courier, monospace;">

<?php
// get video id by channel handler
function getVideoId($channel) {
    // replace if containing @
    $channel = str_replace('@', '', $channel);

    // get channel live video page
    if($videoContent = @file_get_contents('https://www.youtube.com/@'.$channel.'/live')) {
        // get video id
        if(preg_match('/"videoId":"(.*?)"/', $videoContent, $matched) && preg_match('/"isLiveNow":true/', $videoContent)) {
            $videoId = $matched[1];
        }
        else {
            throw new Exception("Current live video not found");
        }
    }
    else {
        throw new Exception("Channel not found");
    }

    return $videoId;
}

// redirect to yt live chat page
function redirect($videoId) {
    ob_end_clean();
    header('Location: https://www.youtube.com/live_chat?v='.$videoId);
    exit();
}

function userInput() {
?>

<!-- get channel from user input -->
<form method="GET">
    <label>YT Channel Handler (without @):</label>
    <input type="text" name="channel">
    <input type="submit">
</form>
<p>
    Or set directly using: yt-livechat-redirect.vercel.app?channel=<strong>Channel-Handler</strong>
</p>
<a href="https://support.google.com/youtube/answer/11585688" target="_blank">What is channel handler?</a>

<?php
}

try {
    if(isset($_GET['channel']) && $_GET['channel']!='') {
        $videoId = getVideoId($_GET['channel']);
        redirect($videoId);
    }
    else {
        userInput();
    }
} catch (Exception $e) {
    echo '<h3 style="color:red">Error - '.$e->getMessage().'</h3>';
}
?>
</body>
</html>
