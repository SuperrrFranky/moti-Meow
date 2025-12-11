<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$html = '
    <div class="day-grid">
        <h2>Daily Check-In</h2>      
        <img id="chestDailyCheckIn" src="/res/image/DailyCheckInChest.png" class="checkInUI" />    
        <div class="day-item">What is Today Reward ? </div>
        
            <button id="claimRewardNow" class="claim-btn">Claim Reward</button>
    </div>';

echo $html;
