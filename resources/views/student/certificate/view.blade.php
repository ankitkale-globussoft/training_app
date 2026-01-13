<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $attempt->test->program->title }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Merriweather:wght@300;400;700&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            font-family: 'Merriweather', serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .btn-download {
            background-color: #696cff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: background 0.3s;
        }

        .btn-download:hover {
            background-color: #5f61e6;
        }

        /* Certificate Container */
        #certificate-container {
            width: 1000px;
            /* A4 Landscape width approx */
            height: 700px;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
        }

        /* Border */
        .border-pattern {
            width: 100%;
            height: 100%;
            border: 5px double #1a237e;
            padding: 20px;
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .border-pattern::before {
            content: '';
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            bottom: 4px;
            border: 2px solid #daa520;
            /* Gold */
            pointer-events: none;
        }

        /* Content */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .app-name {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #555;
            margin-bottom: 30px;
        }

        .title {
            font-family: 'Great Vibes', cursive;
            font-size: 80px;
            color: #1a237e;
            margin: 0;
            line-height: 1;
        }

        .subtitle {
            font-size: 20px;
            margin-top: 10px;
            margin-bottom: 40px;
            color: #333;
        }

        .content-text {
            text-align: center;
            font-size: 18px;
            line-height: 1.6;
            color: #444;
            width: 80%;
            margin: 0 auto;
        }

        .student-name {
            font-size: 42px;
            font-weight: 700;
            color: #daa520;
            /* Gold */
            margin: 20px 0;
            border-bottom: 2px solid #eee;
            display: inline-block;
            padding-bottom: 10px;
            min-width: 400px;
            /* Handling long names */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 90%;
        }

        .program-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a237e;
        }

        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            width: 80%;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            width: 200px;
            border-top: 2px solid #333;
            margin-bottom: 10px;
        }

        .signature-text {
            font-size: 16px;
            font-weight: bold;
        }

        /* Badge/Seal Effect */
        .seal {
            position: absolute;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #daa520;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 5px #fff, 0 0 0 8px #daa520;
            opacity: 0.8;
        }

        .seal span {
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        @media print {
            body {
                background: none;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }

            #certificate-container {
                box-shadow: none;
                margin: 0;
                width: 100%;
                height: 100vh;
                page-break-after: always;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="btn-download" onclick="downloadPDF()">
            Download PDF
        </button>
    </div>

    <div id="certificate-container">
        <div class="border-pattern">
            <div class="header">
                <div class="app-name">{{ env('APP_NAME', 'Learning Platform') }}</div>
                <h1 class="title">Certificate of Completion</h1>
            </div>

            <p class="subtitle">This certificate is proudly presented to</p>

            <div class="student-name">
                {{ $attempt->candidate->name }}
            </div>

            <div class="content-text">
                For successfully completing the assessment for the program<br>
                <div class="program-name">{{ $attempt->test->program->title }}</div>
                <br>
                We acknowledge your dedication and hard work.
            </div>

            <div class="seal">
                <span>Verified</span>
            </div>

            <div class="footer">
                <div class="signature-block">
                    <br><br> <!-- Space for date usually -->
                    <div class="signature-text">{{ $attempt->created_at->format('F d, Y') }}</div>
                    <div class="signature-line" style="margin-top: 5px;"></div>
                    <div>Date</div>
                </div>
                <div class="signature-block">
                    <br><br> <!-- Space for image signature -->
                    <div class="signature-line"></div>
                    <div class="signature-text">Authorized Signature</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('certificate-container');
            const opt = {
                margin: 0,
                filename: 'Certificate_{{ str_replace(" ", "_", $attempt->test->program->title) }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
            };

            // HTML2PDF is loaded from CDN
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>

</html>