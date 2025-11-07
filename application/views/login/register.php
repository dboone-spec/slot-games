<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBC site-domain</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #000;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .logo-container {
            /*margin-bottom: 25px;*/
            text-align: center;
            animation: float 3s ease-in-out infinite;
        }

        .logo {
            width: 200px;
            height: 200px;
            background: #000;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .logo i {
            font-size: 50px;
            color: #6e8efb;
            background: linear-gradient(to right, #6e8efb, #a777e3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-text {
            color: white;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .logo-subtext {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin-top: 5px;
        }

        .container {
            background-color: #000;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            padding: 0 30px 30px 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #fff;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 5px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #9b9b9b;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 46px;
            color: #000;
        }

        .form-group input:focus {
            border-color: #6e8efb;
            outline: none;
            box-shadow: 0 0 0 2px rgba(110, 142, 251, 0.2);
        }

        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .btn {
            background: #0b5ed7;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(110, 142, 251, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .contact-hint {
            display: flex;
            align-items: center;
            margin-top: 5px;
            color: #777;
            font-size: 14px;
        }

        .contact-icon {
            margin-right: 5px;
            width: 16px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .phone-hint {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .logo {
                width: 80px;
                height: 80px;
            }

            .logo i {
                font-size: 40px;
            }

            .logo-text {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
<div class="logo-container">
    <div class="logo">
        <!-- Generator: Adobe Illustrator 25.2.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
        <svg version="1.1" id="Dark" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
             viewBox="0 0 2044 2044" style="enable-background:new 0 0 2044 2044;" xml:space="preserve">
        <style type="text/css">
            .st0{fill:#D5D5D5;}
            .st1{fill:#FE0000;}
        </style>
            <g id="SOFTWARE_copy_4">
                <g>
                    <path class="st0" d="M214.87,1260.84c18.44,0,29.11-0.29,32.02-0.87c2.9-0.58,4.35-2.84,4.35-6.79c0-4.75-2.03-7.74-6.09-8.96
			c-4.06-1.22-14.15-1.83-30.28-1.83c-31.9,0-52.03-2.32-60.38-6.96c-8.35-4.64-12.53-16.12-12.53-34.45
			c0-16.93,5.45-27.78,16.36-32.54c8.35-3.59,26.91-5.39,55.68-5.39c25.64,0,41.87,0.7,48.72,2.09
			c16.12,3.25,24.19,16.94,24.19,41.06h-38.28c0-6.61-1.45-10.47-4.35-11.57c-2.9-1.1-12.94-1.65-30.1-1.65
			c-17.29,0-27.26,0.32-29.93,0.96c-2.67,0.64-4,2.99-4,7.05c0,4.29,2.26,7.08,6.79,8.35c4.52,1.28,13.8,1.91,27.84,1.91
			c31.55,0,51.88,2.5,60.99,7.48c9.1,4.99,13.66,16.47,13.66,34.45c0,16.59-5.11,27.2-15.31,31.84c-8.82,3.94-28.65,5.92-59.51,5.92
			c-23.78,0-39.21-0.52-46.28-1.57c-10.9-1.62-18.33-5.42-22.27-11.4c-3.94-5.97-5.92-16.33-5.92-31.06h38.28
			c0,6.96,1.51,11.02,4.52,12.18C186.04,1260.26,196.66,1260.84,214.87,1260.84z"/>
                    <path class="st0" d="M521.15,1226.73c0,30.28-3.65,48.37-10.96,54.29c-7.89,6.38-31.67,9.57-71.34,9.57
			c-29,0-48.02-1.04-57.07-3.13c-11.37-2.67-18.56-8.47-21.58-17.4c-2.32-6.84-3.48-21.29-3.48-43.33
			c0-30.28,3.59-48.37,10.79-54.29c7.89-6.5,31.67-9.74,71.34-9.74c39.56,0,63.28,3.19,71.17,9.57
			C517.43,1178.3,521.15,1196.46,521.15,1226.73z M395,1226.73c0,15.89,1.83,25.14,5.48,27.75c3.65,2.61,16.44,3.92,38.37,3.92
			c22.04,0,34.89-1.3,38.54-3.92c3.65-2.61,5.48-11.86,5.48-27.75c0-16.01-1.83-25.32-5.48-27.93c-3.65-2.61-16.44-3.92-38.37-3.92
			c-22.04,0-34.89,1.3-38.54,3.92C396.82,1201.42,395,1210.72,395,1226.73z"/>
                    <path class="st0" d="M586.2,1288.85v-124.58h132.94v32.19h-94.66v21.58h89.44v32.19h-89.44v38.63H586.2z"/>
                    <path class="st0" d="M785.71,1196.46v-32.19h148.07v32.19h-54.81v92.39h-38.28v-92.39H785.71z"/>
                    <path class="st0" d="M1255.89,1164.61l-48.2,124.24h-58.64l-21.23-76.21c0-0.23-5.11-0.23-5.22,0l-21.05,76.21h-58.64
			l-48.2-124.24h40.89l34.45,91.87h4.18l28.19-91.87h45.76l28.19,91.87h4.18l34.45-91.87H1255.89z"/>
                    <polygon class="st0" points="1390.29,1256.85 1350.48,1256.85 1380.42,1217.48 1434.7,1288.85 1474.9,1288.85 1380.42,1164.61
			1285.94,1288.85 1414.63,1288.85 		"/>
                    <path class="st0" d="M1537.02,1164.27h111.36c13.69,0,23.37,2.5,29.06,7.5c6.15,5.46,9.22,15.06,9.22,28.78
			c0,10.7-1.28,18.61-3.83,23.72c-2.55,5.12-7.02,8.96-13.4,11.51c6.96,3.02,11.57,6.03,13.83,9.05c2.26,3.02,3.39,8.41,3.39,16.18
			v27.84h-38.28v-22.27c0-3.25-0.87-5.97-2.61-8.18c-2.21-2.9-5.51-4.35-9.92-4.35h-60.55v34.8h-38.28V1164.27z M1575.3,1196.46
			v25.4h61.42c4.18,0,7.16-0.91,8.96-2.72c1.8-1.81,2.7-5.11,2.7-9.9c0-4.79-0.93-8.12-2.78-9.99c-1.86-1.87-4.82-2.8-8.87-2.8
			H1575.3z"/>
                    <path class="st0" d="M1757.82,1288.85v-124.58h133.98v30.1h-95.7v17.4h91.35v29.93h-91.35v17.05h95.7v30.1H1757.82z"/>
                </g>
            </g>
            <path class="st1" d="M1704.01,1052.88c-35.65,0-71.3,0-106.95,0c-4.77-1.05-6.37-4.22-6.13-8.82c0.02-126.69-0.01-253.4,0-380.1
	c0-10.53-0.01-10.54-10.33-10.54c-60.97-0.08-121.95,0.14-182.92-0.08c-5.39-0.27-6.74-1.46-8.03-6.91c-0.01-29.1,0-58.21,0-87.31
	c0-4.17,1.29-6.22,4.88-6.22c170.42,0,341.16,0,511.57,0c3.82,0,5.32,2.26,5.32,6.1c0,29.1,0.01,58.21,0,87.31
	c-1.19,5.5-2.56,6.74-7.9,7.02c-61.76,0.24-123.54-0.01-185.3,0.09c-7.59,0.06-8,0.45-8.07,7.82
	c-0.03,127.29,0.03,254.58-0.03,381.87C1710.25,1047.78,1708.56,1051.2,1704.01,1052.88z"/>
            <path class="st1" d="M1233.37,552.9c63.98-0.08,97.34,28.27,98.54,93.15c-0.41,19.19,0.23,38.61,0,57.98
	c-1.56,3.8-4.79,4.11-8.28,4.11c-31.99-0.03-63.98,0.11-95.96-0.04c-7.11-0.16-9.67-2.63-9.78-9.68c-0.18-12.66,0-25.32-0.09-37.98
	c-0.04-6.54-0.7-7.24-7.15-7.24c-97.13-0.03-194.26-0.03-291.39,0c-6.4,0-7.07,0.71-7.07,7.3c-0.03,92.96-0.03,185.93,0,278.89
	c0,6.83,0.59,7.42,7.46,7.42c96.8,0.03,193.6,0.03,290.4,0c7.13,0,7.75-0.63,7.76-7.67c0.04-25.82,0.04-51.65,0-77.47
	c-0.01-6.98-0.6-7.54-7.85-7.54c-47.98-0.03-95.97-0.01-143.95-0.02c-14.87,0-16.12-1.28-16.13-16.36c0-23.49-0.05-46.98,0.02-70.47
	c0.03-9.44,2.81-12.33,12.19-12.34c86.64-0.08,173.27-0.05,259.91-0.05c4.71,0,8.67,0.75,9.91,6.13
	c-0.81,71.24,1.83,142.9-1.46,213.7c-4.96,55.05-41.36,78.2-93.51,78.2c-117.64,0-220.6,0.1-341.6,0.1
	c-69.71,0-99.77-45.35-99.77-100.27c0-100.13,0-200.26,0-300.38c-1.43-65.06,30.9-99.46,96.53-99.46
	C1005.53,553.83,1119.97,553.89,1233.37,552.9z"/>
            <path class="st1" d="M753.38,1042.75l-300.31-486.8c-1.31-2.13-3.52-3.2-5.73-3.2c-2.21,0-4.41,1.07-5.73,3.2l-300.31,486.8
	c-2.76,4.48,0.46,10.26,5.73,10.26h423.24c4.94,0,7.95-5.42,5.35-9.62l-54.96-88.63c-1.15-1.85-3.17-2.98-5.35-2.98H316.37
	l130.97-212.3l130.97,212.3h0l59.65,96.2c1.94,3.13,5.36,5.03,9.04,5.03h100.66C752.92,1053.01,756.14,1047.23,753.38,1042.75z"/>
</svg>

    </div>
</div>

<div class="container">
    <div class="header">
        <p>Fill out the participation form</p>
    </div>

    <form id="registration-form" method="POST">
        <div class="form-group">
            <label for="name" class="required">Name</label>
            <i class="fas fa-user"></i>
            <input type="text" id="name" name="name" placeholder="John Smith" required>
            <div class="error-message" id="name-error">Please, enter Your name</div>
        </div>

        <div class="form-group">
            <label for="company" class="required">Company</label>
            <i class="fas fa-user"></i>
            <input type="text" id="company" name="company" placeholder="Your company name" required>
            <div class="error-message" id="company-error">Please, enter Your company name</div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" name="email" placeholder="example@mail.com">
            <div class="error-message" id="email-error">Please, enter email</div>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <i class="fas fa-phone"></i>
            <input type="tel" id="phone" name="phone" placeholder="+ phone number">
            <div class="phone-hint">Example: +441234567890</div>
            <div class="error-message" id="phone-error">Please, enter phone number (start with +)</div>
        </div>

        <div class="form-group">
            <label for="telegram">Telegram nickname</label>
            <i class="fab fa-telegram"></i>
            <input type="text" id="telegram" name="telegram" placeholder="@username">
            <div class="contact-hint">
                <span>Specify at least one communication method</span>
            </div>
            <div class="error-message" id="contact-error">Please, specify at least one communication method.</div>
        </div>

        <button type="submit" class="btn">Enter</button>
    </form>

    <div class="footer">
    </div>
</div>

<div class="maintext">
    <p lang="en-US" class="western" align="center" style="margin-bottom: 20px; line-height: 100%">
        <font color="#ffffff">&#127920; <font face="inter, serif"><font size="4" style="font-size: 14pt"><b>Win
                        Exclusive Prizes from AGT Software</b></font></font></font></p>
    <p lang="en-US" class="western" align="center" style="margin-left: -0.02in; margin-bottom: 10px; line-height: 100%">
        <font color="#ffffff">&#127942; <font face="inter, serif"><font size="3" style="font-size: 12pt"><b>A
                        Unique Opportunity for Operators, Aggregators, and Platforms!</b></font></font></font></p>
    <table width="100%" cellpadding="7" cellspacing="0">
        <col width="194"/>

        <col width="477"/>

        <tr valign="top">
            <td width="194" style="border: none; padding: 0in"><p lang="en-US" class="western">
                    <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Dates:</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                September 16&ndash;18,2025</font></font></font></p>
            </td>
            <td width="477" style="border: none; padding: 0in"><p lang="en-US" class="western" align="right">
                    <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Location:</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                            </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">Booth
                                B650, Feira Internacional de Lisboa, Lisbon, Portugal</font></font></font></p>
            </td>
        </tr>
    </table>
    <p lang="en-US" class="western" style="margin-bottom: 0in; line-height: 100%">
        <br/>

    </p>
    <p lang="en-US" class="western" style="margin-left: -0.02in; margin-bottom: 0in; line-height: 100%">
        <font color="#ffffff">&#127922; <font face="inter, serif"><font size="3" style="font-size: 12pt"><b>How
                        to Participate in the Giveaway</b></font></font></font></p>
    <ol style="margin-left: 40px;">
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Visit
                                our Booth</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                            B650 at the SBC Summit 2025</font></font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Register</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                            with our representative to enter the complimentary lottery</font></font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Play
                                several spins </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">on
                            the dedicated lottery website in any AGT Software game</font></font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Claim
                                Your Jackpot</b></font></font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Play
                                the poker mini game </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">by
                            selecting 7 cards and forming a poker combination</font></font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Receive
                                a prize </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">based
                            on the resulting poker combination!</font></font></font></p>
    </ol>
    <p lang="en-US" style="margin-left: 0.5in; margin-bottom: 0in; line-height: 100%">
        <br/>

    </p>
    <p lang="en-US" class="western" align="center" style="margin-left: -0.02in; margin-bottom: 0.08in; line-height: 100%">
        <font color="#ffffff">&#127873;</font><font color="#ffffff"> <font face="inter, serif"><font size="3" style="font-size: 12pt"><b>Prize
                        Pool</b></font></font></font></p>
    <center>
        <table width="100%" cellpadding="4" cellspacing="0">
            <col width="312"/>

            <col width="379"/>

            <tr valign="top">
                <td width="312" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western" align="center">
                        <font color="#ffffff">&#127183;</font><font color="#ffffff">
                            <font face="inter, serif"><font size="2" style="font-size: 11pt">Combination</font></font></font></p>
                </td>
                <td width="379" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western" align="center">
                        <font color="#ffffff">&#127873;</font><font color="#ffffff"> <font face="inter, serif"><font size="2" style="font-size: 11pt">Your
                                    Prize</font></font></font></p>
                </td>
            </tr>
            <tr valign="top">
                <td width="312" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff">&#128293;</font><font color="#ffffff">
                        </font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Fifth
                                    </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>jackpot</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    (</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">Royal
                                    Flush)</font></font></font></p>
                </td>
                <td width="379" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>ONE
                                        MONTH FREE ACCESS </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">to
                                    All AGT Software Games</font></font></font></p>
                </td>
            </tr>
            <tr valign="top">
                <td width="312" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff">&#9889;</font><font color="#ffffff"> </font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Fourth
                                        jackpot</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    (</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">Straight
                                    flush, Four of a kind, Full house, Flush)</font></font></font></p>
                </td>
                <td width="379" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>BRANDED
                                        GAME</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    for the Partner</font></font></font></p>
                </td>
            </tr>
            <tr valign="top">
                <td width="312" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff">&#128142;</font><font color="#ffffff">
                        </font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Third
                                    </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>jackpot</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    (</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">Straight,
                                    Three of a kind)</font></font></font></p>
                </td>
                <td width="379" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>20%
                                        DISCOUNT</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    for One Month</font></font></font></p>
                </td>
            </tr>
            <tr valign="top">
                <td width="312" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff">&#127919;</font><font color="#ffffff">
                        </font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Second
                                    </b></font></font></font><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>jackpot</b></font></font><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                (</font></font><font color="#0000ff"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><span style="text-decoration: none">Two
				pair</span></font></font></font></font><font face="inter, serif"><font size="2" style="font-size: 11pt">)</font></font></p>
                </td>
                <td width="379" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>10%
                                        DISCOUNT </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">for
                                    One Month</font></font></font></p>
                </td>
            </tr>
            <tr valign="top">
                <td width="312" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff">&#127914;</font><font color="#ffffff">
                        </font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>First
                                        jackpot</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    (</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">One
                                    pair, High card)</font></font></font></p>
                </td>
                <td width="379" style="border: 1px solid #767171; padding: 0.04in 0.08in"><p lang="en-US" class="western">
                        <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>EXCLUSIVE
                                        AGT software SOUVENIRS</b></font></font></font></p>
                </td>
            </tr>
        </table>
    </center>
    <ul style="margin-left: 40px;">
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font face="inter, serif"><font size="2" style="font-size: 11pt">Prizes
                        are valid upon partner integration</font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font face="inter, serif"><font size="2" style="font-size: 11pt">All
                        prizes apply to both existing integrations and new connections</font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font face="inter, serif"><font size="2" style="font-size: 11pt">If
                        multiple partner employees participate in the promotion, results are
                        not cumulative; only the best result will be considered</font></font></p>
        <li><p lang="en-US" style="margin-bottom: 0in; line-height: 100%"><font face="inter, serif"><font size="2" style="font-size: 11pt">All
                        prizes are valid throughout 2025</font></font></p>
    </ul>
    <p lang="en-US" style="margin-left: 0.5in; margin-bottom: 0in; line-height: 100%">
        <br/>

    </p>
    <table width="100%" cellpadding="7" cellspacing="0">
        <col width="168"/>

        <col width="515"/>

        <tr valign="top">
            <td width="168" style="border: none; padding: 0in"><p lang="en-US" class="western" style="margin-left: -0.06in; margin-bottom: 0in">
                    <font color="#ffffff">&#128231;</font><font color="#ffffff"> <font face="inter, serif"><font size="3" style="font-size: 12pt"><b>HOW
                                    TO CLAIM</b></font></font></font></p>
                <p lang="en-US" class="western" align="right" style="margin-left: -0.06in">
                    <font color="#ffffff"><font face="inter, serif"><font size="3" style="font-size: 12pt"><b>YOUR
                                    PRIZE?</b></font></font></font></p>
            </td>
            <td width="515" style="border: none; padding: 0in">
                <ul style="margin-left: 40px;"><li><p lang="en-US" style="margin-bottom: 0in"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">After
                                        the game, you will receive a flyer with </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>a
                                            unique code</b></font></font></font></p>
                    <li><p lang="en-US" class="western" style="margin-bottom: 0in"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">After
                                        the exhibition, please </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>email
                                            us</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                        at </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>info@site-domain.com
                                        </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">Include
                                        your name, company name, and the received code, attach a photo of
                                        the flyer, and </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>claim
                                            your prize</b></font></font></font></p>
                    <li><p lang="en-US" class="western"><font face="inter, serif"><font size="2" style="font-size: 11pt">You
                                    can follow these instructions via the link sbc.site-domain.org</font></font></p>
                </ul>
            </td>
        </tr>
    </table>
    <p lang="en-US" style="margin-left: 0.5in; margin-bottom: 0in; line-height: 100%">
        <br/>

    </p>
    <table width="100%" cellpadding="7" cellspacing="0">
        <col width="168"/>

        <col width="515"/>

        <tr valign="top">
            <td width="168" style="border: none; padding: 0in"><p lang="en-US" style="margin-left: -0.06in; margin-bottom: 0in">
                    <font color="#ffffff">&#128188;</font><font color="#ffffff"> <font face="inter, serif"><font size="3" style="font-size: 12pt"><b>WHO
                                    CAN </b></font></font></font>
                </p>
                <p lang="en-US" align="right" style="margin-left: -0.06in"><font color="#ffffff"><font face="inter, serif"><font size="3" style="font-size: 12pt"><b>PARTICIPATE?</b></font></font></font></p>
            </td>
            <td width="515" style="border: none; padding: 0in"><p lang="en-US" class="western" style="margin-left: 15px;">
                    <font color="#ffffff">&#9989;</font><font color="#ffffff"> </font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Current</b></font></font></font>
                    <font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">AGT
                                Software Partners<br/>
                            </font></font></font><font color="#ffffff"><font face="Segoe UI Emoji, serif"><font size="2" style="font-size: 11pt">&#9989;</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                            </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>New
                                    Clients</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                and Prospective Partners<br/>
                            </font></font></font><font color="#ffffff"><font face="Segoe UI Emoji, serif"><font size="2" style="font-size: 11pt">&#9989;</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                            </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Casino
                                    Operators</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                of All Sizes<br/>
                            </font></font></font><font color="#ffffff"><font face="Segoe UI Emoji, serif"><font size="2" style="font-size: 11pt">&#9989;</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                            </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Game
                                    Aggregators </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">and</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>
                                </b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">Platforms<br/>
                            </font></font></font><font color="#ffffff"><font face="Segoe UI Emoji, serif"><font size="2" style="font-size: 11pt">&#9989;</font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                            </font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>B2B
                                    Representatives</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                of the Gaming Industry</font></font></font></p>
            </td>
        </tr>
    </table>
    <p lang="en-US" style="margin-left: 0.5in; margin-bottom: 0in; line-height: 100%">
        <br/>

    </p>
    <table width="100%" cellpadding="7" cellspacing="0">
        <col width="168"/>

        <col width="515"/>

        <tr valign="top">
            <td width="186" style="border: none; padding: 0in"><p lang="en-US" style="margin-left: -0.06in">
                    <font color="#ffffff">&#128205; <font face="inter, serif"><font size="3" style="font-size: 12pt"><b>WHERE
                                    to FIND US?</b></font></font></font></p>
            </td>
            <td width="515" style="border: none; padding: 0in"><p lang="en-US" class="western" style="margin-bottom: 0in">
                    <font color="#ffffff">&#127970; <font face="inter, serif"><font size="2" style="font-size: 11pt"><b>AGT
                                    Software Booth</b></font></font></font></p>
                <ul style="margin-left: 40px;">
                    <li><p lang="en-US" style="margin-bottom: 0in"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">Booth
                                        B650, SBC Summit 2025, Feira Internacional de Lisboa</font></font></font></p>
                </ul>
                <p lang="en-US" class="western" style="margin-bottom: 0in"><font color="#ffffff">&#128241;
                        <font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Contacts:</b></font></font></font></p>
                <ul style="margin-left: 40px;">
                    <li><p lang="en-US" class="western" style="margin-bottom: 0in"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Email:</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    </font></font></font><a href="mailto:info@site-domain.com"><font face="inter, serif"><font size="2" style="font-size: 11pt"><u>info@site-domain.com</u></font></font></a></p>
                    <li><p lang="en-US" class="western" style="margin-bottom: 0in"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Website:</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                    </font></font></font><a href="https://site-domain.com/"><font face="inter, serif"><font size="2" style="font-size: 11pt"><u>site-domain.com</u></font></font></a></p>
                    <li><p lang="en-US" class="western"><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt"><b>Telegram
                                            support:</b></font></font></font><font color="#ffffff"><font face="inter, serif"><font size="2" style="font-size: 11pt">
                                        @site-domain_support</font></font></font></p>
                </ul>
            </td>
        </tr>
    </table>
    <p lang="en-US" class="western" style="margin-bottom: 0.08in; line-height: 0.17in">
        <br/>
        <br/>

    </p>
    <p lang="en-US" class="western" align="center" style="margin-bottom: 0.15in; line-height: 150%">
        <font color="#ffffff"><font face="inter, serif"><b>#SBCSummit2025
                    #site-domain #CasinoGames #iGaming #Lisbon2025</b></font></font></p>
    <table width="100%" cellpadding="7" cellspacing="0">
        <col width="304"/>

        <col width="367"/>

        <tr valign="top">
            <td width="304" style="border: none; padding: 0in"><p lang="en-US"><img src="data:image/png;base64,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" name="Ð Ð¸ÑÑƒÐ½Ð¾Ðº 23" align="bottom" width="112" height="48" border="0"/>
                </p>
            </td>
            <td width="367" style="border: none; padding: 0in"><p lang="en-US" class="western" align="right" style="margin-left: 0.13in; margin-bottom: 0in">
                    <a href="https://site-domain.com/"><font face="Square721 BT, serif"><font size="2" style="font-size: 10pt">site-domain.com</font></font></a></p>
                <p lang="en-US" class="western" align="right" style="margin-left: 0.13in; margin-bottom: 0in">
                    <a href="mailto:info@site-domain.com"><font face="Square721 BT, serif"><font size="2" style="font-size: 10pt">info@site-domain.com</font></font></a></p>
                <p lang="en-US" class="western" align="right" style="margin-left: 0.13in">
                    <font color="#ffffff"><font face="Square721 BT, serif"><font size="2" style="font-size: 10pt">@site-domain_support</font></font></font></p>
            </td>
        </tr>
    </table>
    <p lang="en-US" class="western" align="center" style="margin-bottom: 0.15in; line-height: 150%">
        <br/>
        <br/>

    </p>
    <style>
        .maintext {
            color: #fff;
            width: 100%;
            max-width: 860px;
        }
        a {
            font-weight: bold;
            color: cornflowerblue;
        }
    </style>
</div>

<script>
    document.getElementById('registration-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const company = document.getElementById('company').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const telegram = document.getElementById('telegram').value.trim();

        // Сброс сообщений об ошибках
        document.querySelectorAll('.error-message').forEach(msg => {
            msg.style.display = 'none';
        });

        let isValid = true;

        // Проверка имени (обязательное поле)
        if (name === '') {
            document.getElementById('name-error').style.display = 'block';
            isValid = false;
        }

        // Проверка имени (обязательное поле)
        if (company === '') {
            document.getElementById('company-error').style.display = 'block';
            isValid = false;
        }

        // Проверка email, если заполнен
        if (email !== '') {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById('email-error').style.display = 'block';
                isValid = false;
            }
        }

        // Проверка телефона, если заполнен
        if (phone !== '') {
            // Упрощенная проверка международного номера - начинается с + и содержит только цифры после +
            const phonePattern = /^\+\d{6,15}$/;
            if (!phonePattern.test(phone)) {
                document.getElementById('phone-error').style.display = 'block';
                isValid = false;
            }
        }

        // Проверка, что хотя бы одно контактное поле заполнено
        if (email === '' && phone === '' && telegram === '') {
            document.getElementById('contact-error').style.display = 'block';
            isValid = false;
        }

        if (isValid) {
            // В реальном приложении здесь был бы код для отправки данных на сервер
            let contactMethod = '';
            if (email !== '') contactMethod = `Email: ${email}`;
            if (phone !== '') contactMethod = `Телефон: ${phone}`;
            if (telegram !== '') contactMethod = `Telegram: ${telegram}`;

            // Очистка формы
            this.submit();
            this.reset();
        }
    });

    // Простая маска ввода для телефона - автоматическое добавление +
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value;

        // Если значение не начинается с +, добавляем его
        if (value && !value.startsWith('+')) {
            // Удаляем все нецифровые символы, кроме +
            value = value.replace(/[^\d+]/g, '');

            // Если первый символ не +, добавляем его
            if (!value.startsWith('+')) {
                value = '+' + value;
            }

            e.target.value = value;
        }
    });

    // При фокусе на поле телефона, если оно пустое, добавляем +
    document.getElementById('phone').addEventListener('focus', function(e) {
        if (!e.target.value) {
            e.target.value = '+';
        }
    });
</script>
</body>
</html>