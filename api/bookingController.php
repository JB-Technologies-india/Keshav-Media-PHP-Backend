<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$response = [
    "status"  => 500,
    "success" => false,
    "message" => "Something went wrong. Please try again later.",
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response["status"]  = 405;
    $response['success'] = false;
    $response["message"] = "Method Not Allowed.";
    echo json_encode($response);
    exit;
}

$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$service        = trim($_POST['service'] ?? '');
$location       = trim($_POST['location'] ?? '');
$preferred_date = trim($_POST['preferred_date'] ?? '');
$message        = trim($_POST['message'] ?? '');

if ($name === '') {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Please enter your name.";
    echo json_encode($response);
    exit;
}

if ($email === '') {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Please enter your email address.";
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Please enter a valid email address.";
    echo json_encode($response);
    exit;
}

if ($service === '') {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Please select a service.";
    echo json_encode($response);
    exit;
}

if (strlen($name) > 100) {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Name cannot exceed 100 characters.";
    echo json_encode($response);
    exit;
}

if (strlen($phone) > 30) {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Phone number is too long.";
    echo json_encode($response);
    exit;
}

if (strlen($location) > 255) {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Location is too long.";
    echo json_encode($response);
    exit;
}

if (strlen($message) > 1000) {
    $response["status"]  = 400;
    $response['success'] = false;
    $response["message"] = "Message cannot exceed 1000 characters.";
    echo json_encode($response);
    exit;
}

$name           = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email          = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$phone          = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$service        = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
$location       = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
$preferred_date = htmlspecialchars($preferred_date, ENT_QUOTES, 'UTF-8');
$message        = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

require_once __DIR__ . '/../mail.php';

$supportEmail = $sender_email_id;

// $adminEmail =  'hello@keshavmedia.ca';
$adminEmail = $sender_email_id;

$adminSubject = "Booking Request - {$service} | {$name} | Keshav Media";
$userSubject = "Keshav Media Request Received | Thank you, {$name}";

$adminBody = '
<html>
<body style="margin:0;padding:0;background:#f7fafc;font-family:Arial, sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7fafc;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="700" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 14px 40px rgba(15,23,42,0.08);">
                    <tr>
                        <td style="background:#111827;padding:24px 32px;text-align:center;color:#ffffff;">
                            <h2 style="margin:0;font-size:24px;">    Booking Request</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px;font-size:15px;color:#334155;"> A new booking request has been submitted through the Keshav Media website. Please review the customer details below and contact them as soon as possible. </p>
                            <h3 style="margin:0 0 15px;color:#111827;font-size:18px;"> Customer Details </h3>
                            <table width="100%" cellpadding="12" cellspacing="0" style="border-collapse:collapse;border:1px solid #e5e7eb;border-radius:12px;background:#f8fafc;">
                                <tr style="background:#ffffff;">
                                    <td style="width:180px;font-weight:700;color:#111827;">Name</td>
                                    <td style="color:#111827;">'.$name.'</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;color:#111827;">Email</td>
                                    <td style="color:#111827;">'.$email.'</td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="font-weight:700;color:#111827;">Phone</td>
                                    <td style="color:#111827;">'.($phone ?: 'Not provided').'</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;color:#111827;">Service</td>
                                    <td style="color:#111827;">'.$service.'</td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="font-weight:700;color:#111827;">Location</td>
                                    <td style="color:#111827;">'.($location ?: 'Not provided').'</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;color:#111827;">Preferred Date</td>
                                    <td style="color:#111827;">'.($preferred_date ?: 'Not provided').'</td>
                                </tr>
                                <tr>
                                    <td valign="top" style="font-weight:700;color:#111827;">Message</td>
                                    <td style="color:#111827;line-height:1.75;">'.$message.'</td>
                                </tr>
                            </table>
                            <p style="margin:24px 0 0;font-size:14px;color:#64748b;"> Please review the information above and contact the customer at your earliest convenience. </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
';

$userBody = '
<html>
<body style="margin:0;padding:0;background:#f1f5f8;font-family:Arial, sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f8;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="700" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 18px 45px rgba(15,23,42,0.09);">
                    <tr>
                        <td style="background:#0d1b2a;padding:28px 32px;text-align:center;">
                            <h1 style="margin:0;font-size:28px;color:#ffffff;font-weight:700;">Keshav Media</h1>
                            <p style="margin:8px 0 0;font-size:16px;color:#cbd5e1;">Thank you — we’ve received your request.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 20px;font-size:16px;line-height:1.75;color:#334155;">Hi '.$name.',</p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:1.75;color:#475569;">We have received your request and our team will contact you soon to discuss your project and next steps.</p>
                            <table width="100%" cellpadding="14" cellspacing="0" style="border-collapse:collapse;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;">
                                <tr style="background:#ffffff;">
                                    <td style="width:180px;font-weight:700;color:#111827;">Name</td>
                                    <td style="color:#111827;">'.$name.'</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;color:#111827;">Email</td>
                                    <td style="color:#111827;">'.$email.'</td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="font-weight:700;color:#111827;">Phone</td>
                                    <td style="color:#111827;">'.($phone ?: 'Not provided').'</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;color:#111827;">Service</td>
                                    <td style="color:#111827;">'.$service.'</td>
                                </tr>
                                <tr style="background:#ffffff;">
                                    <td style="font-weight:700;color:#111827;">Location</td>
                                    <td style="color:#111827;">'.($location ?: 'Not provided').'</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:700;color:#111827;">Preferred Date</td>
                                    <td style="color:#111827;">'.($preferred_date ?: 'Not provided').'</td>
                                </tr>
                                <tr>
                                    <td valign="top" style="font-weight:700;color:#111827;">Message</td>
                                    <td style="color:#111827;line-height:1.75;">'.$message.'</td>
                                </tr>
                            </table>
                            <div style="margin-top:28px;padding:20px;background:#eff6ff;border-left:4px solid #2563eb;border-radius:12px;">
                                <p style="margin:0;font-size:14px;color:#1d4ed8;">If you have additional details to share, reply to this email or contact us at <a href="mailto:'.$supportEmail.'" style="color:#2563eb;text-decoration:none;">'.$supportEmail.'</a>.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#111827;padding:22px 32px;text-align:center;color:#e2e8f0;font-size:14px;">
                            <p style="margin:0;">Keshav Media &middot; Photography, Videography & Drone Media</p>
                            <p style="margin:6px 0 0;color:#9ca3af;">Delivered with precision across Saskatchewan.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
';

$adminResult = sendMail($adminEmail, $adminSubject, $adminBody);
// $userResult = sendMail($email, $userSubject, $userBody);

if ($adminResult['success']) {

    $response['status']  = 200;
    $response['success'] = true;
    $response['message'] = "Thank you! Your request has been submitted successfully. We will contact you soon.";

} else {

    $response['status']  = 500;
    $response['success'] = false;
    $response['message'] = "Sorry! We could not submit your booking request at this time. Please try again later." . $adminResult['message'];
}

echo json_encode($response);
exit;

