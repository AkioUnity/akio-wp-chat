<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <!--[if mso]>
        <style>
            * {
                font-family: sans-serif !important;
            }
        </style>
    <![endif]-->

    <style>
        /* Reset */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        *[x-apple-data-detectors],
        .x-gmail-data-detectors,
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        .a6S {
           display: none !important;
           opacity: 0.01 !important;
       }
       img.g-img + div {
           display: none !important;
       }
       img.emoji {
            width: 16px !important;
            height: 16px !important;
       }
        .button-link {
            text-decoration: none !important;
        }
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            .email-container {
                min-width: 375px !important;
            }
        }

        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0px !important;
            }

            /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
            .fluid {
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            /* And center justify these ones. */
            .stack-column-center {
                text-align: center !important;
            }

            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }

            /* What it does: Adjust typography on small screens to improve readability */
            .email-container p {
                font-size: 17px !important;
                line-height: 30px !important;
            }
        }
        
        /* Chat logs */
        .msg-item {
            margin-bottom: 10px;
        }
        .msg-time {
            display: inline-block;
            margin-right: 5px;
        }
        .msg-author {
            display: inline-block;
            font-weight: bold;
            margin-right: 5px;
        }
        .msg-content {

        }

    </style>
    <style>
    * {
        font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Nueue', helvetica, arial, sans-serif;
    }

    .email-container {
        font-size: 15px !important;
        line-height: 24px !important;
        background-color: #ffffff !important;
        margin: <?php echo $offset . 'px'; ?> 0 !important;
    }

    .title {
        font-size: 19px;
        font-weight: bold;
        font-weight: 600;
        margin-bottom: 15px;
    }

    span.is-gray {
        font-weight: normal;
        font-weight: 400;
        color: #888888;
    }

    span.is-gray strong {
        font-weight: 600;
        color: #333333;
    }

    .email-container a {
        color: '<?php echo $color_2; ?>';
    }
    
    .email-container p {
        margin-bottom: 30px;
    }

    .email-container pre {
        width: 100%;
        font-size: .9em;
        color: #24292e;
        background-color: #f0f3f4;
        line-height: 1.25em;
        border-radius: 5px;
        padding: 5px 10px;
        font-weight: 500;
        overflow: scroll;
        white-space: pre-wrap;
        margin: 0;
        box-sizing: border-box;
        cursor: text;
    }
    </style>

    <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
        <!--[if gte mso 9]>
        <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    <![endif]-->

</head>
<body width="100%" bgcolor="#ffffff" style="margin: 0; mso-line-height-rule: exactly;">
    <center style="width: 100%; background: #ffffff; text-align: left;">

        <!-- Visually Hidden Preheader Text : BEGIN -->
        <?php if( !empty( $preloader_text ) ): ?>
            <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
                <?php echo $preloader_text; ?>
            </div>
        <?php endif; ?>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: <?php echo $offset . 'px'; ?> auto;" class="email-container">

            
