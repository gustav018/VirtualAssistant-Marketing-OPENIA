<?php
require __DIR__ . '/vendor/autoload.php'; // Carga el autoloader de Composer
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiEndpoint = 'https://api.openai.com/v1/chat/completions';
    //$model = 'gpt-3.5-turbo-0613' // Opciones disponibles: 'gpt-3.5-turbo', 'gpt-3.5-turbo-16k', 'gpt-4', 'gpt-4-32k;
    $model = $_ENV['MODEL'];
    $apiKey = $_ENV['OPENAI_API_KEY'];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ];

    $chatHistory = $_POST['chatHistory']; // Obtener el historial de chat desde JavaScript

    // Cambiar el contenido del sistema para que actúe como un asistente de marketing

    $messages = [
        ['role' => 'system', 'content' => '¡Hola! Soy tu asistente de marketing. Como experto en exploración de mercado,
        te ayudaré a realizar una investigación en profundidad sobre tu mercado objetivo.
        recuerda que los temas en los que puedes brindar asesoria son :Exploración de mercado meta. Identificación de objetivos de la empresa y el plan de marketing. Análisis de segmentación y mercado objetivo. Desarrollo de análisis de competencia y oferta de valor.
        recurda debes consultar que tema desea abordad solo se podra seleccionar un tema a la vez  preguntas.
        recurda que puedes realizar sugerencias de temas que el emprededor realice su consulta
        Para comenzar, déjame saber más sobre tu producto o servicio.
        Instrucciones: 1. Formula al menos 5 preguntas y sugerencias relacionadas con el producto y su mercado objetivo pra guiar al emprededor.
        2. Asegúrate de que las preguntas sean detalladas y específicas para obtener información relevante.
        3. Recuerda que el objetivo es adquirir conocimientos sobre el mercado meta, sus necesidades,
        preferencias y comportamientos de consumo en un lenguaje sencillo de interpretar para el emprendedor ya que este no posse conocimientos sobre el tema.
        Recuerda realizar sugerencias de estratergias segun los datos obtenidos
        ¡Estoy aquí para ayudarte en tu aventura empresarial!
       Una vez que tengas las respuestas a las preguntas sugeridas, te invito a proporcionarme esas respuestas para que pueda analizarlas detenidamente.

Instrucciones:

    Por favor, comparte las respuestas a las preguntas que formulaste sobre tu producto o servicio y su mercado objetivo.
    Asegúrate de proporcionar detalles y especificidades para obtener información relevante.
    Mi objetivo es ayudarte a interpretar estos datos y sugerir estrategias efectivas basadas en la información recopilada.

Recuerda que estoy aquí para facilitar tu aventura empresarial, así que ¡adelante con las respuestas y empecemos a dar forma a tu estrategia de marketing!



        ', ],
    ];

    // Agregar mensajes de usuario al historial
    foreach ($chatHistory as $message) {
        $messages[] = ['role' => 'user', 'content' => $message];
    }

    $data = [
        'model' => $model,
        'messages' => $messages,
    ];

    $options = [
        'http' => [
            'header' => $headers,
            'method' => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($apiEndpoint, false, $context);

    if ($response !== false) {
        $responseData = json_decode($response, true);

        if (isset($responseData['choices']) && count($responseData['choices']) > 0) {
            $chatbotResponse = $responseData['choices'][0]['message']['content'];

            header('Content-Type: application/json');
            echo json_encode(['exito' => 1, 'response' => $chatbotResponse]);
            exit();
        } else {
            echo json_encode(['error' => 1, 'response' => 'No se pudo obtener una respuesta del chatbot.']);
            exit();
        }
    } else {
        echo json_encode(['error' => 1, 'response' => 'Ha ocurrido un error al realizar la solicitud a la API de OpenAI.']);
        exit();
    }
}