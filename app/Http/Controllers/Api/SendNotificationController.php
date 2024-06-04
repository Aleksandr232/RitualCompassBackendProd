<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Ritual;
use App\Models\QuestionsRitual;
use App\Models\TelegramContact;

class SendNotificationController extends Controller
{
     /**
 * @OA\Post(
 *     path="/api/send/{id}",
 *     summary="Отправка заявки в телеграмм",
 *     description="Отправка заявки",
 *     tags={"Главная страница"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="message",
 *                     type="string"
 *                 ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Заявка успешно отправленна."
 *             )
 *         )
 *     )
 *
 * )
 */

    public function sendTelegram(Request $request, $id)
    {
        $company = Ritual::find($id);
        $companyName = $company ? $company->company_ritual : 'Неизвестно';

        $telegram = new TelegramContact();
        $telegram -> phone = $request -> phone;
        $telegram -> name = $request -> name;
        $telegram -> message = $request -> message;
        $telegram -> phone = $request -> phone;
        $telegram->ritual_company = $companyName;

                $formattedMessage = "🚀 Новая заявка!\n\n"
                . "🙋‍♂️ Имя: *$request->name*\n"
                . "📞 Телефон: `$request->phone`\n"
                . "📝 Сообщение: \n$request->message\n\n"
                . "🏢 Компания: *$companyName*";


        $this->sendTelegramMessage($formattedMessage);

        $telegram->save();

        return response()->json(['message' => 'Заявка успешно отправленна'], 200);
    }

    /**
 * @OA\Post(
 *     path="/api/question",
 *     summary="Отправка вопросника в телеграмм",
 *     description="Отправка заявки",
 *     tags={"Главная страница"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="material",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="clothes",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="decoration",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="documents",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="place",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="taxi",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="services",
 *                     type="string"
 *                 ),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Заявка успешно отправленна."
 *             )
 *         )
 *     )
 *
 * )
 */

    public function sendTelegramQuestions(Request $request)
    {
        $questions = new QuestionsRitual([
            'name' => $request->name,
            'phone' => $request->phone,
            'material' => $request->material,
            'clothes' => $request->clothes,
            'decoration' => $request->decoration,
            'documents' => $request->documents,
            'place' => $request->place,
            'taxi' => $request->taxi,
            'services' => $request->services,
        ]);

        $questions->save();

        $QuestionMessage = "🚀 Новая заявка!\n\n"
            . "👤 Имя: *" . $request->name . "*\n"
            . "📞 Телефон: `" . $request->phone . "`\n"
            . "🛒 Материал: *" . $request->material . "*\n"
            . "👕 Одежда: *" . $request->clothes . "*\n"
            . "💐 Украшения: *" . $request->decoration . "*\n"
            . "📄 Документы: *" . $request->documents . "*\n"
            . "🪦 Место: *" . $request->place . "*\n"
            . "🚕 Такси: *" . $request->taxi . "*\n"
            . "🛠 Услуги: *" . $request->services . "*\n\n"
            . "🙏 Спасибо за заявку! Мы свяжемся с вами в ближайшее время.";

        $this->sendQuestions($QuestionMessage);

        return response()->json(['message' => 'Заявка успешно отправленна'], 200);
    }

     /**
 * @OA\Post(
 *     path="/api/phone/{id}",
 *     summary="Отправка уведомления о звонке в телеграмм",
 *     description="Отправка уведомления",
 *     tags={"Главная страница"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Заявка успешно отправленна."
 *             )
 *         )
 *     )
 *
 * )
 */

    public function sendPhone(Request $request, $id)
    {
        $company = Ritual::find($id);
        $companyName = $company ? $company->company_ritual : 'Неизвестно';


                $PhoneMessage = "📞 Звонок!\n\n"
                . "🏢 Компания: *$companyName*";


        $this->sendTelegramPhone($PhoneMessage);

        

        return response()->json(['message' => 'Заявка успешно отправленна'], 200);
    }

    private function sendTelegramMessage($formattedMessage)
    {
        $botToken = config('secret.telegram.bot_token');
        $chatId = config('secret.telegram.chat_id');

        $url = "https://api.telegram.org/bot$botToken/sendMessage";



        $data = [
            'chat_id' => $chatId,
            'text' => $formattedMessage,
            'parse_mode' => 'Markdown',
        ];

        Http::post($url, $data);
    }

    private function sendQuestions($QuestionMessage)
    {
        $botToken = config('secret.telegram.bot_token');
        $chatId = config('secret.telegram.chat_id');

        $url = "https://api.telegram.org/bot$botToken/sendMessage";



        $data = [
            'chat_id' => $chatId,
            'text' => $QuestionMessage,
            'parse_mode' => 'Markdown',
        ];

        Http::post($url, $data);
    }

    private function sendTelegramPhone($PhoneMessage)
    {
        $botToken = config('secret.telegram.bot_token');
        $chatId = config('secret.telegram.chat_id');

        $url = "https://api.telegram.org/bot$botToken/sendMessage";



        $data = [
            'chat_id' => $chatId,
            'text' => $PhoneMessage,
            'parse_mode' => 'Markdown',
        ];

        Http::post($url, $data);
    }

}

