<div id="tidio-wrapper">

    <div id="tidio-chat-wp-plugin">
        
        <div id="welcome-text">
            <h1>Hi there! Great to see you’ve installed Tidio Chat.</h1>
            <p>Select one of the options below and your integration will be complete. If you’ve already got a Tidio account, the current project will be connected to that account. However, if you don’t have an account with us (or you’d like to create a new one) - just select the option on the right.</p>
        </div>
        <div id="after-install-text">
            <h1>Your site is already integrated with Tidio Chat</h1>
            <p>All you need to do now is select the “Tidio Chat” tab on the left - that will take you to your Tidio panel. You can also open the panel by using the link below.</p>
            <p><a href="#" id="open-panel-link" class="button button-primary tidio-button" target="_blank" style="margin-left: auto; margin-right: auto; display: block;">Go to Panel</a></p>
        </div>
        
        
        <div class="error"></div>
        
        <div id="input-blocks">
            <div id="tidio-chat-login">
                <h2>Yes, I have an account with Tidio</h2>
                <div class="form-control">
                    <input type="text" value="" id="tidio-login-input" class="text-input" placeholder="Enter your e-mail...">
                </div>
                <div class="form-control">
                    <input type="password" value="" id="tidio-password-input" class="text-input" placeholder="Type password...">
                </div>
                <a href="#" id="tidio-login-button" class="button tidio-button" target="_blank">Login</a>
            </div>
            <div id="tidio-chat-panel">
                <h2>No, I do not have an account with Tidio</h2>
                <div id="tidio-apply-account">
                    That's alright, you can start using Tidio without having the login.
                </div>
                <a href="#" id="redirect-to-panel" class="button button-primary tidio-button" target="_blank">Create a new account</a>
            </div>
        </div>
        
        <div id="select-project">
            <h2>Select project:</h2>
            <div class="form-control">
                <select name="select-tidio-project" id="select-tidio-project"></select>
            </div>
            <a href="#" id="get-tidio-project" style="margin-left:auto;margin-right:auto;display:block;" class="button button-primary tidio-button" target="_blank">Select</a>
        </div>
    </div>
</div>