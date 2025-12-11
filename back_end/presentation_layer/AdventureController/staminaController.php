<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../business_logic_layer/AdventureService/staminaService.php';
require_once '../../business_logic_layer/InventoryService/inventoryService.php';
require_once '../../data_access_layer/inventoryDAO.php';

$userId = $_SESSION['user_id'] ?? 1;

$inventoryService = new InventoryService($userId);
$recoveryItemQty = StaminaService::getRecoveryItemQty($userId);
$currentStamina = StaminaService::getCurrentStamina($userId);
$recoveryItemNeed = 5 - $currentStamina;

if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    $action = $_POST['action'];

    switch ($action) {
        case 'checkStamina':
            $result = StaminaService::checkStamina($userId);
            echo json_encode($result);
            break;

        case 'useRecoveryItem':
            $currentStamina = StaminaService::getCurrentStamina($userId);
            $recoveryItemNeed = 5 - $currentStamina;

            $result_deductReItem = $inventoryService->useItem(47, $recoveryItemNeed);
            $result_updateStamina = StaminaService::updateStamina($userId, $recoveryItemNeed);

            if ($result_deductReItem && $result_updateStamina['status'] === 'success') {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode([
                    'status' => 'fail',
                    'message' => 'Cannot use recovery item or failed to update stamina'
                ]);
            }
            break;

        case 'getEnergy':
            $result = StaminaService::getUserEnergyWithAutoRecovery($userId);
            echo json_encode($result);
            break;

        case 'startAdventure':
            $_SESSION['adventure_in_progress'] = true;
            $_SESSION['claimed_adventure'] = false;
            $result = StaminaService::startAdventure($userId);
            echo json_encode($result);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}

// ---------------------------
// If no POST, serve Popup based on GET parameter
// ---------------------------
$popupType = $_GET['popupType'] ?? 'insufficient'; // default to insufficient

if ($popupType === 'insufficient') {
?>

    <div class="ins-adventure-popup">
        <div class="ins-popup-header">
            <h1>Stamina Insufficient</h1>
            <img src="../../../res/image/adventure/warning-icon.png" alt="warning-icon" class="ins-warning-icon">
        </div>
        <?php if (($recoveryItemQty != 0) && (($recoveryItemQty + $currentStamina) >= 5)) { ?>
            <h2 class="ins-popup-question">Do you want to use recovery item?</h2>
            <div class="ins-stamina-info">
                <div class="ins-stamina-row">
                    <p>Current Recovery item :</p>
                    <img src="../../../res/image/adventure/Mouse.gif" alt="stamina-icon" class="ins-stamina-icon">
                    <p><?php echo $recoveryItemQty; ?></p>
                </div>
                <div class="ins-stamina-row">
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;Use :</p>
                    <img src="../../../res/image/adventure/Mouse.gif" alt="stamina-icon" class="ins-stamina-icon">
                    <p><?php echo $recoveryItemNeed; ?></p>
                </div>
            </div>
            <div class="conf-button-group">
            <button class="conf-no-button" onclick="document.getElementById('adventurePopupContainer').classList.add('hidden'); document.getElementById('adventure-popup-overlay').classList.add('hidden');">NO</button>
            <button class="conf-yes-button" onclick="useRecoveryItem()">YES</button>
            </div>
        <?php } else if (($recoveryItemQty + $currentStamina) < 5) { ?>
            <h2 class="ins-popup-question">Hang tight! Your stamina is auto-recharging and you'll be back in action soon </h2>
            <div class="conf-button-group">
            <button class="conf-yes-button" onclick="document.getElementById('adventurePopupContainer').classList.add('hidden'); document.getElementById('adventure-popup-overlay').classList.add('hidden');">OKAY</button>
            </div>
        <?php } ?>

    </div>
        </div>
<?php
} elseif ($popupType === 'adventure') {
?>

    <div class="conf-adventure-popup">
        <h1>Do you want to send your cat to adventure?</h1>
        <div class="conf-stamina-info">
            <p>Required Stamina:</p>
            <img src="../../../res/image/adventure/Mouse.gif" alt="mouse-icon" class="ins-stamina-icon">
            <span>5</span>
        </div>
        <div class="conf-button-group">
            <button class="conf-no-button" onclick="document.getElementById('adventurePopupContainer').classList.add('hidden'); document.getElementById('adventure-popup-overlay').classList.add('hidden');">NO</button>
            <button class="conf-yes-button" onclick="startAdventure()">YES</button>
        </div>
    </div>
<?php
}
?>