<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
    </head>
    <body>
        <div>
            <p><?php echo $title;?></p>
            <p>APP_ACTION:<?php echo APP_ACTION;?></p>
            <p>APP_VIEW:<?php echo APP_VIEW;?></p>
            result:
            <pre><?php var_dump($title);?></pre>
        </div>
    </body>
</html>
