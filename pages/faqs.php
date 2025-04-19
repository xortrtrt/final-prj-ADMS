<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs</title>
    <style>
        body {
            font-family: "Roboto", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: 
                /* 22% dark overlay */
                linear-gradient(rgba(0, 0, 0, 0.22), rgba(0, 0, 0, 0.22)),
                /* Background image */
                url('../assets/images/campus-slider-main-1.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .faq-item {
            margin-bottom: 20px;
        }

        .faq-question {
            background: #2e7d32;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            text-align: left;
            transition: background-color 0.3s ease;
        }

        .faq-question:hover {
            background: #1b5e20;
        }

        .faq-answer {
            display: none;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 16px;
            line-height: 1.6;
        }

        .faq-answer p {
            margin: 0;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-button:hover {
            background-color: #1b5e20;
            transform: translateY(-2px);
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const faqQuestions = document.querySelectorAll(".faq-question");
            faqQuestions.forEach(question => {
                question.addEventListener("click", function () {
                    const answer = this.nextElementSibling;
                    if (answer.style.display === "block") {
                        answer.style.display = "none";
                    } else {
                        answer.style.display = "block";
                    }
                });
            });
        });
    </script>
</head>
<body>

<div class="container">
    <h1>Frequently Asked Questions</h1>

    <div class="faq-item">
        <button class="faq-question">How do I report a lost item?</button>
        <div class="faq-answer">
            <p>You can report a lost item by navigating to the "Report Lost" page and filling out the form with the required details.</p>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">How do I claim a found item?</button>
        <div class="faq-answer">
            <p>To claim a found item, go to the "Report Found" page, search for the item, and follow the instructions to verify ownership.</p>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">What should I do if I need help?</button>
        <div class="faq-answer">
            <p>If you need assistance, please visit the "Contact Us" page and reach out to us via email or phone. We're here to help!</p>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">What happens after I report a lost item?</button>
        <div class="faq-answer">
            <p>Once you report a lost item, it will be added to our database. If someone finds an item matching your description, we will notify you via email or phone.</p>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">Can I edit or delete my report?</button>
        <div class="faq-answer">
            <p>Yes, you can edit or delete your report by logging into your account, navigating to your dashboard, and selecting the appropriate action for your report.</p>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">Is my personal information safe?</button>
        <div class="faq-answer">
            <p>Yes, we take your privacy seriously. Your personal information is stored securely and is only used to facilitate communication between you and the person who found your item.</p>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">What types of items can I report?</button>
        <div class="faq-answer">
            <p>You can report any type of lost or found item, including electronics, documents, jewelry, clothing, and more. Please provide as much detail as possible to help identify the item.</p>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">How long does it take to match a lost item with a found item?</button>
        <div class="faq-answer">
            <p>The time it takes to match a lost item with a found item depends on the details provided and the availability of matching reports. We recommend providing accurate and detailed descriptions to improve the chances of a match.</p>
        </div>
    </div>

    <button class="back-button" onclick="window.location.href='login.php';">Back to Home</button>
</div>

</body>
</html>