<!DOCTYPE html>
<html>
    <head>
        <title>{$title}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
    </head>
    <body>
        <div>
            <p>{$title}</p>
            <p>APP_ACTION:{$APP_ACTION}</p>
            <p>APP_VIEW:{$APP_VIEW}</p>
            result:
            <pre>{$result|var_dump}</pre>
        </div>
    </body>
</html>
