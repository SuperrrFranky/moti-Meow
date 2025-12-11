<?php
require_once '../../business_logic_layer/AdventureService/adventureService.php';
require_once '../../business_logic_layer/InventoryService/inventoryService.php';
require_once '../../../_base.php';
$userId = $_SESSION['user_id'] ?? 1;

$inventoryService = new InventoryService($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'startOrContinueAdventure') {
        $result = AdventureService::startOrContinueAdventure($userId);
        // print($result);
        echo json_encode($result);
        exit;
    }

    if ($action === 'rewardPlayer') {
        $adventure = AdventureService::getLatestCompletedAdventure($userId);
    
        if (!$adventure) {
            echo json_encode(['status' => 'fail', 'message' => 'No completed adventure found']);
            exit;
        }
    
        $adventureID = $adventure['adventureID'];
        $rewardID = $adventure['reward'];
    
        if ($rewardID) {
            // Reward already exists — fetch item info
            $item = $inventoryService->getItemByID($rewardID); // You may need to implement this method in inventoryService
            if (!$item) {
                echo json_encode(['status' => 'fail', 'message' => 'Reward item not found']);
                exit;
            }
    
            if ($item['category'] === 'head' || $item['category'] === 'body' || $item['category'] === 'neck') {
                $path = '../res/image/accessories/' . $item['category'] . ' accessories/' . $item['imgPath'];
            } else {
                $path = '../res/image/roomDeco/' . $item['category'] . '/' . $item['imgPath'];
            }
    
            echo json_encode([
                'status' => 'success',
                'imgPath' => $path,
                'name' => $item['name'],
                'adventure_title' => $adventure['adventure_title']
            ]);
            $_SESSION['adventure_in_progress'] = false;
            $_SESSION['claimed_adventure'] = true;
            AdventureService::markRewardClaimed($adventureID);
            exit;
        }
    
        // No reward yet, then generate and mark it
        $reward = $inventoryService->rewardPlayer();
    
        $rewardID = ($reward === "you got them all") ? 47 : $reward->getID();
        AdventureService::markAdventureReward($userId, $adventureID, $rewardID);
    
        $path = ($reward === "you got them all") ? '../res/image/adventure/mouse.gif'
            : (
                ($reward->getCategory() === 'head' || $reward->getCategory() === 'body' || $reward->getCategory() === 'neck')
                    ? '../res/image/accessories/' . $reward->getCategory() . ' accessories/' . $reward->getImage()
                    : '../res/image/roomDeco/' . $reward->getCategory() . '/' . $reward->getImage()
            );
    
        $name = ($reward === "you got them all") ? 'Stamina Refill' : $reward->getName();
    
        echo json_encode([
            'status' => 'success',
            'imgPath' => $path,
            'name' => $name,
            'adventure_title' => $adventure['adventure_title']
        ]);
        $_SESSION['adventure_in_progress'] = false;
        $_SESSION['claimed_adventure'] = true;
        AdventureService::markRewardClaimed($adventureID);
        exit;
    }
    

    if ($action === 'getAdventureDetail' && isset($_POST['adventureID'])) {
        $adventureId = $_POST['adventureID'];
        $adventure = AdventureService::getAdventureByID($adventureId);
        echo json_encode($adventure);
        exit;
    }
}
