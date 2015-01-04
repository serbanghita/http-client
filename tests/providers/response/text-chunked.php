<?php
header('Transfer-encoding: chunked');
header('Content-type: application/json');

$chunk1Content = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>This is the title</title>
    </head>';
$chunk1Length = dechex(strlen($chunk1Content));

$chunk2Content = '<body>
        <p>The web page body content.</p>
    </body>
</html>';
$chunk2Length = dechex(strlen($chunk2Content));

$chunk3Content = '<script>
alert(1);
</script>';
$chunk3Length = dechex(strlen($chunk3Content));

$output = $chunk1Length . "\r\n" .
            $chunk1Content . "\r\n" .
            $chunk2Length . "\r\n" .
            $chunk2Content . "\r\n" .
            $chunk3Length . "\r\n" .
            $chunk3Content . "\r\n" .
            '0' . "\r\n" .
            "\r\n";
echo $output;

echo strlen($chunk1Content . $chunk2Content . $chunk3Content);