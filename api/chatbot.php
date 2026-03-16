<?php
header('Content-Type: application/json');

// Get the raw POST data
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

if(!isset($input['message'])) {
    echo json_encode(['success' => false, 'error' => 'No message provided']);
    exit;
}

// Include DB config for live prices and reports
require_once '../db/config.php';

$userMessage = $input['message'];
$history = isset($input['history']) ? $input['history'] : [];

// ==========================================
// GEMINI API INTEGRATION
// ==========================================
$gemini_api_key = 'AIzaSyDn-aXDncfz9dSrMLXosnDG8KzFf2XMpQs'; 

if ($gemini_api_key !== 'YOUR_GEMINI_API_KEY_HERE' && !empty($gemini_api_key)) {
    // Call Gemini API
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $gemini_api_key;
    
    // Fetch live catalog
    $catalog = "";
    $res = mysqli_query($conn, "SELECT name, price FROM packages LIMIT 25");
    if($res) {
        while($row = mysqli_fetch_assoc($res)) {
            $catalog .= $row['name'] . " (Rs " . $row['price'] . "), ";
        }
    }

    $prompt = "You are an expert Virtual Assistant for MyLab Diagnostic Center. 
    1. Understand health queries or symptoms.
    2. Suggest relevant pathology/blood/imaging tests that MyLab provides.
    3. Keep your response concise (max 3-4 sentences), polite, and easy to read. You can use markdown bolding (**text**) for test names.
    4. Do not provide a medical diagnosis or prescribe medicine. Always politely add that they should also consult a doctor.
    5. CRITICAL INSTRUCTION: Reply strictly in the language the user used (Hindi/English).
    6. LIVE PRICES: We offer these tests: " . $catalog . " Recommend from this list and quote prices when helpful.
    7. REPORTS: If user asks for their report, ask for their 10-digit mobile number. If they provide it, output exactly [REPORT:their_number].
    8. BOOKING: If user wants to book a test, output exactly [BOOK:Test Name].";

    $contents = array();
    
    foreach($history as $msg) {
        if(isset($msg['role']) && isset($msg['parts'][0]['text'])) {
            $role = ($msg['role'] == 'model') ? 'model' : 'user';
            $contents[] = array("role" => $role, "parts" => array(array("text" => $msg['parts'][0]['text'])));
        }
    }
    
    // Always append current user message
    $contents[] = array("role" => "user", "parts" => array(array("text" => $userMessage)));

    $data = array(
        "systemInstruction" => array(
            "role" => "user",
            "parts" => array(
                array("text" => $prompt)
            )
        ),
        "contents" => $contents,
        "generationConfig" => array(
            "temperature" => 0.7
        )
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix for XAMPP SSL certificate issues
    $response = curl_exec($ch);
    
    // Debugging curl error if any
    if (curl_errno($ch)) {
        error_log("Curl error: " . curl_error($ch));
    }
    
    curl_close($ch);

    $resData = json_decode($response, true);
    
    if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
        $aiReply = $resData['candidates'][0]['content']['parts'][0]['text'];
        
        // Post-processing for dynamic actions
        // 1. Report Download Link
        if (preg_match('/\[REPORT:(\d+)\]/', $aiReply, $matches)) {
            $mobile = mysqli_real_escape_string($conn, $matches[1]);
            $rep = mysqli_query($conn, "SELECT report_file FROM bookings WHERE mobile='$mobile' AND report_file IS NOT NULL AND report_file != '' ORDER BY id DESC LIMIT 1");
            if($rep && mysqli_num_rows($rep) > 0) {
                $row = mysqli_fetch_assoc($rep);
                $downloadBtn = "<br><br><a href='admin/".$row['report_file']."' target='_blank' style='display:inline-block;background:#10b981;color:white;padding:8px 16px;border-radius:8px;text-decoration:none;font-weight:600;margin-top:10px;'><i class='fas fa-download'></i> Download Report</a>";
                $aiReply = str_replace($matches[0], $downloadBtn, $aiReply);
            } else {
                $aiReply = str_replace($matches[0], "<br><br><span style='color:#ef4444;background:#fef2f2;padding:6px 12px;border-radius:6px;font-size:12px;'>Sorry, no reports found for $mobile.</span>", $aiReply);
            }
        }
        
        // 2. Direct Booking Link
        if (preg_match('/\[BOOK:(.*?)\]/', $aiReply, $matches)) {
            $testNameRaw = htmlspecialchars(trim($matches[1]));
            $bookBtn = "<br><br><a href='pages/booking.php?test=".urlencode($testNameRaw)."' style='display:inline-block;background:#4f46e5;color:white;padding:8px 16px;border-radius:8px;text-decoration:none;font-weight:600;margin-top:10px;'><i class='fas fa-shopping-cart'></i> Book $testNameRaw Now</a>";
            $aiReply = str_replace($matches[0], $bookBtn, $aiReply);
        }

        echo json_encode(['success' => true, 'reply' => $aiReply]);
        exit;
    } else {
        // If API fails (e.g., due to invalid key), we log the error and fall back to manual responses below.
        error_log("Gemini API Error Response: " . print_r($resData, true));
    }
}

// ==========================================
// FALLBACK RULE-BASED LOGIC (If API Key is missing)
// ==========================================
$msgLower = strtolower($userMessage);
$reply = "";

// Simple language detection based on keywords (Hinglish/Hindi vs English)
$isHindi = false;
if (strpos($msgLower, 'kya') !== false || strpos($msgLower, 'bukhar') !== false || strpos($msgLower, 'dard') !== false || strpos($msgLower, 'hai') !== false || strpos($msgLower, 'kamzori') !== false || strpos($msgLower, 'thakan') !== false || strpos($msgLower, 'kaun sa') !== false || preg_match('/\p{Devanagari}/u', $userMessage)) {
    $isHindi = true;
}

if (strpos($msgLower, 'fever') !== false || strpos($msgLower, 'bukhar') !== false) {
    if (strpos($msgLower, 'body ache') !== false || strpos($msgLower, 'dard') !== false || strpos($msgLower, 'pain') !== false) {
        if ($isHindi) {
            $reply = "बुखार और शरीर में दर्द के लिए, मैं आपको **Complete Blood Count (CBC)** और वायरल इन्फेक्शन चेक करने के लिए **Dengue NS1 Antigen** टेस्ट करवाने की सलाह देता हूँ। \n\n<a href='pages/booking.php'>अपना टेस्ट बुक करने के लिए यहाँ क्लिक करें</a>।\n\n*कृपया सटीक इलाज के लिए डॉक्टर से ज़रूर मिलें।*";
        } else {
            $reply = "Based on fever and body ache, I recommend getting a **Complete Blood Count (CBC)** and a **Dengue NS1 Antigen** test to check for viral infections. \n\n<a href='pages/booking.php'>Click here to Book your Tests</a>.\n\n*Please consult a doctor for a proper diagnosis.*";
        }
    } else {
        if ($isHindi) {
            $reply = "अज्ञात बुखार के लिए, डॉक्टर आमतौर पर **Complete Blood Count (CBC)**, **Widal Test (Typhoid)**, और **Urine Routine** की सलाह देते हैं। \n\n<a href='pages/booking.php'>अपना Fever Panel यहाँ बुक करें</a>।\n\n*कृपया मेडिकल सलाह के लिए डॉक्टर से मिलें।*";
        } else {
            $reply = "For an unexplained fever, doctors generally recommend a **Complete Blood Count (CBC)**, **Widal Test (Typhoid)**, and **Urine Routine**. \n\n<a href='pages/booking.php'>Book your Fever Panel here</a>.\n\n*Please consult a doctor for medical advice.*";
        }
    }
} elseif (strpos($msgLower, 'sugar') !== false || strpos($msgLower, 'diabetes') !== false || strpos($msgLower, 'mitha') !== false || strpos($msgLower, 'madhumeh') !== false) {
    if ($isHindi) {
        $reply = "डायबिटीज या ब्लड शुगर लेवल चेक करने के लिए, आपको **HbA1c**, **Fasting Blood Sugar (FBS)**, और **PPBS** टेस्ट करवाने चाहिए। \n\n<a href='pages/booking.php'>हमारे डायबिटीज पैकेजेस यहाँ देखें</a>।\n\n*कृपया अपने डॉक्टर से सलाह लें।*";
    } else {
        $reply = "To check for diabetes or blood sugar levels, you should opt for **HbA1c (Glycosylated Hemoglobin)**, **Fasting Blood Sugar (FBS)**, and **PPBS**. \n\n<a href='pages/booking.php'>Check our Diabetes Packages</a>.\n\n*Please consult your doctor.*";
    }
} elseif (strpos($msgLower, 'weak') !== false || strpos($msgLower, 'kamzori') !== false || strpos($msgLower, 'fatigue') !== false || strpos($msgLower, 'thakan') !== false) {
    if ($isHindi) {
        $reply = "लगातार कमज़ोरी न्यूट्रिशनल कमी या थायराइड की वजह से हो सकती है। आप अपना **Vitamin D**, **Vitamin B12**, **Iron Profile**, और **Thyroid Profile (TSH)** चेक करवा सकते हैं। \n\n<a href='pages/booking.php'>Full Body Checkup बुक करें</a>।\n\n*कृपया अपने डॉक्टर से सलाह लें।*";
    } else {
        $reply = "Continuous weakness might be due to nutritional deficiency or thyroid issues. You could consider checking your **Vitamin D**, **Vitamin B12**, **Iron Profile**, and **Thyroid Profile (TSH)**. \n\n<a href='pages/booking.php'>Book a Full Body Checkup</a>.\n\n*Please consult your doctor.*";
    }
} elseif (strpos($msgLower, 'thyroid') !== false || strpos($msgLower, 'weight gain') !== false || strpos($msgLower, 'hair fall') !== false || strpos($msgLower, 'vajan') !== false || strpos($msgLower, 'baal') !== false) {
    if ($isHindi) {
        $reply = "अचानक वज़न बढ़ना या बाल गिरना थायराइड असंतुलन से जुड़ा हो सकता है। इसके लिए **Thyroid Profile (T3, T4, TSH)** सबसे सही टेस्ट रहेगा। \n\n<a href='pages/booking.php'>अपना थायराइड टेस्ट बुक करें</a>।\n\n*हमेशा डॉक्टर की सलाह लें।*";
    } else {
        $reply = "Symptoms like unexpected weight changes or hair fall can be linked to thyroid imbalances. A **Thyroid Profile (T3, T4, TSH)** would be the best test to start with. \n\n<a href='pages/booking.php'>Book Thyroid Test</a>.\n\n*Always consult a doctor.*";
    }
} elseif (strpos($msgLower, 'hello') !== false || strpos($msgLower, 'hi') !== false || strpos($msgLower, 'hey') !== false || strpos($msgLower, 'namaste') !== false) {
    if ($isHindi || strpos($msgLower, 'namaste') !== false) {
        $reply = "नमस्ते! 👋 मैं आपका वर्चुअल हेल्थ असिस्टेंट हूँ। कृपया मुझे अपने लक्षणों (symptoms) के बारे में बताएँ, और मैं आपको सबसे सही लैब टेस्ट का सुझाव दूँगा।";
    } else {
        $reply = "Hello! 👋 I am your Virtual Health Assistant. Please tell me about the symptoms you are experiencing, and I'll suggest the most relevant diagnostic lab tests for you.";
    }
} else {
    // Generic fallback
    if ($isHindi) {
        $reply = "मैं समझ गया। सामान्य हेल्थ चेकअप के लिए, **Complete Blood Count (CBC)** और **Comprehensive Metabolic Panel (CMP)** करवाना शरीर की स्थिति जानने के लिए अच्छा रहता है। \n\nज्यादा सही सुझाव के लिए, क्या आप अपनी समस्या के बारे में थोड़ा और बता सकते हैं (जैसे बुख़ार, कमज़ोरी, या दर्द)?\n\n*(नोट: मैं अभी ऑफलाइन मोड में चल रहा हूँ। स्मार्ट जवाब के लिए Gemini API Key ऐड करें।)*";
    } else {
        $reply = "I understand. Based on general wellness checks, a **Complete Blood Count (CBC)** and **Comprehensive Metabolic Panel (CMP)** are a good starting point to check overall health. \n\nTo get a better suggestion, could you be slightly more specific about your symptoms (like fever, weakness, pain)?\n\n*(Note: I am running in offline mode as the AI API key is not yet configured. Add Gemini API key for smarter responses.)*";
    }
}

// Add artificial delay to feel like AI is "thinking"
sleep(1);

echo json_encode(['success' => true, 'reply' => $reply]);
?>
