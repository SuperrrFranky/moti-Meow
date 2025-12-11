<?php
require_once(__DIR__ . '../../../data_access_layer/adventureDAO.php');

class AdventureService
{
    public static function startOrContinueAdventure($userId)
    {
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // Step 1: Check if there is any adventure going on
        $adventure = AdventureDAO::getOngoingAdventure($userId);

        if ($adventure) {
            $endTime = new DateTime($adventure['endingTime']);
            $now = new DateTime();
            $secondsRemaining = $endTime->getTimestamp() - $now->getTimestamp();

            if ($secondsRemaining <= 0) {
                // Return completed status and keep background
                return [
                    'status' => 'completed',
                    'backgroundImage' => $adventure['background_media'],
                    'secondsRemaining' => 0
                ];
            }

            return [
                'status' => 'success',
                'backgroundImage' => $adventure['background_media'],
                'secondsRemaining' => $secondsRemaining
            ];
        }

        // Step 2: No adventure in progress -> Create a new adventure
        $background = AdventureDAO::getRandomBackground();

        if (!$background) {
            return ['status' => 'fail', 'message' => 'No backgrounds available'];
        }

        $background_key = $background['background_key'];

        $messages = [
            [
                "role" => "system",
                "content" => "You are a cute and supportive cat who is narrating its own adventure. Format your response in one line only: start with a short adventure title followed by a period, then continue with 4–6 short sentences telling the story in first person. Everything must relate to the theme: '$background_key'. Your story should include a clear goal, a small challenge or surprise, and end with a sense of discovery, growth, or emotional reward. Use soft, playful, and encouraging language that reflects your cute and supportive personality. Do not include any labels, explanations, or extra lines. Use concise, expressive sentences to capture a full mini-arc including a goal, a small challenge, and a heartwarming ending."
            ],
            [
                "role" => "user",
                "content" => "Tell me your adventure story, brave cat!"
            ]
        ];

        $response = callAzureOpenAI($messages, 600);

        $firstDot = strpos($response, '.');
        $title = trim(substr($response, 0, $firstDot));
        $story = trim(substr($response, $firstDot + 1));

        $now = new DateTime();
        $end = clone $now;
        $end->modify('+100 sec');

        $insertSuccess = AdventureDAO::createAdventure(
            $userId,
            $background['background_id'],
            $now->format('Y-m-d H:i:s'),
            $end->format('Y-m-d H:i:s'),
            $title,
            $story
        );

        if (!$insertSuccess) {
            return ['status' => 'fail', 'message' => 'Failed to start adventure'];
        }

        return [
            'status' => 'success',
            'backgroundImage' => $background['background_media'],
            'secondsRemaining' => 100 // 1hour
        ];
    }

    public static function getCurrentAdventureTitle($userId)
    {
        $adventure = AdventureDAO::getCurrentAdventure($userId);
        return $adventure['adventure_title'] ?? null;
    }

    public static function updateReward($userId, $reward)
    {
        AdventureDAO::updateReward($userId, $reward);
    }

    public static function getAdventureList($userId)
    {
        return AdventureDAO::getAllAdventure($userId);
    }

    public static function getAdventureByID($adventureId)
    {
        return AdventureDAO::getAdventureByID($adventureId);
    }

    public static function getLatestCompletedAdventure($userId)
    {
        return AdventureDAO::getLatestCompletedAdventure($userId);
    }

    public static function markAdventureReward($userId, $adventureId, $rewardId)
    {
        return AdventureDAO::updateAdventureReward($userId, $adventureId, $rewardId);
    }

    public static function markRewardClaimed($adventureId)
    {
        return AdventureDAO::markRewardClaimed($adventureId);
    }
}
