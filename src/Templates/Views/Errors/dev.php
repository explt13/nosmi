<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
</head>
<body style="margin: 0; padding: 0; min-height:100vh; background-color: rgba(140, 105, 105, 0.3);">
    <div class="wrapper">
        <main class="page">
            <section class="error">
                <div class="error__text" style="box-sizing: border-box; padding: 10px; background-color: #de6868; font-size: 36px;"><?= $err_type ?> has occured</div>
                <table class="error__subtext" style="width: 100%; font-size: 22px; border-collapse:collapse">
                    <tr style="background-color: rgba(240, 105, 105, 0.2);">
                        <td style="padding: 15px 30px;"><b>Message</b></td>
                        <td><?=$err_message?></td>
                    </tr>
                    <tr style="background-color: rgba(240, 105, 105, 0.1);">
                        <td style="padding: 15px 30px;"><b>File</b></td>
                        <td><?=$err_file?></td>
                    </tr>
                    <tr style="background-color: rgba(240, 105, 105, 0.2);">
                        <td style="padding: 15px 30px;"><b>Line</b></td>
                        <td><?=$err_line?></td>
                    </tr>
                    <tr style="background-color: rgba(240, 105, 105, 0.1);">
                        <td style="padding: 15px 30px;"><b>Response</b></td>
                        <td><?=$err_response?></td>
                    </tr>
                </table>
                <div class="err-callstack">
                    <button class="err-callstack__show" style="margin: 20px 0 0 20px; font-size: 18px; border-radius: 4px; padding: 10px;">Show callstack</button>
                    <div class="err-callstack__body" style="margin: 0 0 0 40px; font-size: 16px" hidden>
                        <?php
                            use Explt13\Nosmi\Utils\Debug;
                            Debug::printReadable($callstack);
                        ?>
                    </div>
                </div>
            </section>
        </main>
        <script>
            const callstack = document.querySelector('.err-callstack');
            const button = callstack.querySelector('.err-callstack__show');
            const callstackBody = callstack.querySelector('.err-callstack__body');
            button.addEventListener('click', () => {
                if (callstackBody.hidden) {
                    callstackBody.hidden = false;
                } else {
                    callstackBody.hidden = true;
                }
            })
        </script>
    </div>
</body>
</html>