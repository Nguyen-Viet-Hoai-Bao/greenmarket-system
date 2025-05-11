<?php

// app/Services/DialogflowService.php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\TextInput;

class DialogflowService
{
    protected $sessionClient;

    public function __construct()
    {
        // Tạo đối tượng SessionsClient của Dialogflow
        $this->sessionClient = new SessionsClient();
    }

    public function sendTextQuery($text, $projectId = 'hadoop-chatbot-hnc9')
    {
        // Tạo Session ID ngẫu nhiên
        $sessionId = uniqid();
        $session = $this->sessionClient->sessionName($projectId, $sessionId);

        // Tạo TextInput với văn bản từ người dùng
        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode('vi'); // Thay thế 'en' nếu bạn muốn ngôn ngữ khác

        // Tạo QueryInput từ TextInput
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        // Gửi yêu cầu đến Dialogflow và nhận phản hồi
        $response = $this->sessionClient->detectIntent($session, $queryInput);

        // Lấy câu trả lời từ Dialogflow
        $queryResult = $response->getQueryResult();
        return $queryResult->getFulfillmentText(); // Trả về câu trả lời của Dialogflow
    }
}
