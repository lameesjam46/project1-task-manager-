<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .message {
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.8;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>إعادة تعيين كلمة المرور</h1>
        </div>
        
        <div class="content">
            <div class="message">
                <p>مرحباً،</p>
                <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p>
                <p>إذا لم تقم بهذا الطلب، يمكنك تجاهل هذا البريد الإلكتروني.</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/reset-password/' . $token) }}" class="button">
                    إعادة تعيين كلمة المرور
                </a>
            </div>
            
            <div class="warning">
                <strong>تنبيه:</strong> هذا الرابط صالح لمدة 60 دقيقة فقط. بعد انتهاء المدة، ستحتاج إلى طلب رابط جديد.
            </div>
            
            <div class="message">
                <p>إذا لم يعمل الزر أعلاه، يمكنك نسخ الرابط التالي ولصقه في متصفحك:</p>
                <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace;">
                    {{ url('/reset-password/' . $token) }}
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>هذا البريد الإلكتروني تم إرساله من نظام إدارة المهام</p>
            <p>إذا لم تقم بطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني.</p>
        </div>
    </div>
</body>
</html> 