<!-- User Manual Popup -->
<div id="helpOverlay" class="help-overlay hidden"></div>
<div id="userManualPopup" class="popup1 hidden">
    <div class="popup-header">
        <h2>User Manual</h2>
        <button id="closeUserManual" class="close-btn">&times;</button>
    </div>
    <table class="manual-table">
        <tr>
            <td><img src="/res/image/interaction/fish-bones.png" class="icon" /></td>
            <td>Feed meow</td>
        </tr>
        <tr>
            <td><img src="/res/image/interaction/chat-gpt.png" class="icon" /></td>
            <td>Chat with meow</td>
        </tr>
        <tr>
            <td><img src="/res/image/interaction/click.png" class="icon" /></td>
            <td>Interact with meow</td>
        </tr>
        <tr>
            <td><img src="/res/image/interaction/click.png" class="icon" /></td>
            <td>Double click to stop interact with meow</td>
        </tr>
        <tr>
            <td><img src="/res/image/exercise.png" class="icon" /></td>
            <td>Activity Pack</td>
        </tr>
        <tr>
            <td><img src="/res/image/history.png" class="icon" /></td>
            <td>Adventure Story</td>
        </tr>
        <tr>
            <td><img src="/res/image/decoration.png" class="icon" /></td>
            <td>Decoration Home</td>
        </tr>
        <tr>
            <td><img src="/res/image/calendar.png" class="icon" /></td>
            <td>Daily Check-In</td>
        </tr>
        <tr>
            <td><img src="/res/image/profile_pic.png" class="icon" /></td>
            <td>Cat Profile</td>
        </tr>
        <tr>
            <td><img src="/res/image/door.png" class="icon" /></td>
            <td>Cat Adventure</td>
        </tr>
        <tr>
            <td><img src="/res/image/photoGallery1.png" class="icon" /></td>
            <td>Photo Gallery</td>
        </tr>
    </table>
</div>

<!-- Styles for popup (ensure these are loaded once) -->
<style>
    .popup1 {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #f1e0d6;
        border-radius: 16px;
        padding: 20px;
        width: 300px;
        max-height: 80vh;
        /* overflow-y: auto; */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        z-index: 1500;
        overflow: auto;
        scrollbar-width: none;
    }

    .hidden {
        display: none;
    }

    .popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .popup-header h2 {
        margin: 0;
        font-size: 1.2rem;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 1.4rem;
        cursor: pointer;
    }

    .manual-table {
        width: 100%;
        border-collapse: collapse;
    }

    .manual-table tr+tr {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .manual-table td {
        padding: 8px;
        vertical-align: middle;
    }

    .icon {
        width: 40px;
        height: 30px;
    }
</style>