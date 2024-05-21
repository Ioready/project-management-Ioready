<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    Subject: "HR Department / Company to send payslips by email at time of confirmation of payslip."
</p>

<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    Dear {{ $payslip_name }},
</p>
<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    Hope this email finds you well! Please see attached payslip for {{ $payslip_salary_month }}. Simply click on the button below:
</p>
<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px; text-align: center;">
    <a href="{{ $payslip_url }}" style="display: inline-block; padding: 10px 20px; font-size: 14px; color: #fff; background-color: #007bff; border-radius: 5px; text-decoration: none;">
        View Payslip
    </a>
</p>
<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px; text-align: center;">
    <a href="{{ $pdf }}" style="display: inline-block; padding: 10px 20px; font-size: 14px; color: #fff; background-color: #00ff73; border-radius: 5px; text-decoration: none;">
        Download Payslip
    </a>
</p>

<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    Feel free to reach out if you have any questions.
</p>
<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    Regards,
</p>
<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    HR Department,
</p>
<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    {{ config('app.name') }}
</p>
<p style="font-family: Segoe UI, Arial; font-size: 14px; line-height: 28px;">
    {{ config('app.url') }}
</p>
