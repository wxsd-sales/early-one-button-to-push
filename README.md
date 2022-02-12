Early One Button to Push
========================
**Customize the timing of OBTP on your shared Webex devices.**

Conventionally, you can configure the OBTP to show at 0, 5, 10 or 15 minutes before the meeting time at an organizational level (via. Webex Control Hub settings).
However, this proof-of-concept application lets you set any custom value (in seconds) for the OBTP to appear, that too at a device level.
The target audience for this PoC are individuals who want to have greater control over the timing of the OBTP.
For example, those who would like to join a meeting on a Webex device more than 15 minutes ahead of the start time.


<p align="center">
   <a href="https://www.youtube.com/watch?v==87s" target="_blank" alt="See the video demo.">
       <img src="https://user-images.githubusercontent.com/6129517/153688689-7d4329e2-bf3a-44e6-9506-5e2904164bbc.gif" alt="early-one-button-to-push-demo"/>
    </a>
</p>


<!-- ⛔️ MD-MAGIC-EXAMPLE:START (TOC:collapse=true&collapseText=Click to expand) -->
<details>
<summary>Table of Contents (click to expand)</summary>

* [Overview](#overview)
* [Setup](#setup)
* [Demo](#demo)
* [Support](#support)

</details>
<!-- ⛔️ MD-MAGIC-EXAMPLE:END -->

## Overview
At it's core, the application is a collection of background processes that run on a predefined schedule.

These processes, collectively, retrieve a user's calendar events and create corresponding bookings for those on devices that are mapped to the user.

The front end serves as a way to "map" or "unmap" calendar accounts to shared devices. 
All bookings are done via a bot account that has admin privileges to the shared devices (on Webex Control Hub).


## Setup

These instructions assume that you have:
- Administrator access to an Azure AD Tenant and Webex Control Hub.
- Set the target devices as [shared devices](https://help.webex.com/en-US/article/1mqb9cb/Add-Shared-Devices-and-Services-to-a-Workspace).
- [Docker installed](https://docs.docker.com/engine/install/) and running on a Windows (via WSL2), macOS, or Linux machine.

Open a new terminal window and follow the instructions below.

1. Clone this repository and change directory
   ```
   git clone https://github.com/WXSD-Sales/early-one-button-to-push && cd early-one-button-to-push
   ```

2. Rename `.env.example` file to `.env` (you may also edit your database credentials within this renamed file)
   ```
   mv .env.example .env
   ```

3. Review and follow the [Quickstart: Register an application with the Microsoft identity platform](https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app#register-an-application) guide.
    - Select the following [Microsoft Graph API permissions](https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-configure-app-access-web-apis#delegated-permission-to-microsoft-graph):
   
      | API / Permissions name | Type      | Description                                         |
      |------------------------|-----------|-----------------------------------------------------|
      | Calendars.Read         | Delegated | Read user calendars                                 |
      | email                  | Delegated | View users' email address                           |
      | offline_access         | Delegated | Maintain access to data you have given it access to |
      | openid                 | Delegated | Sign users in                                       |
      | profile                | Delegated | View users' basic profile                           |
      | User.Read              | Delegated | Sign in and read user profile                       |
    - Use these [Redirect URIs](https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app#add-a-redirect-uri):
        - `https://localhost/auth/azure/callback`
        - `http://localhost/auth/azure/callback`
    - Take note of your [Azure Tenant ID](https://docs.microsoft.com/en-us/azure/active-directory/fundamentals/active-directory-how-to-find-tenant), Application ID and, Client Secret. Assign these values to the `AZURE_TENANT_ID`, `AZURE_CLIENT_ID`, and `AZURE_CLIENT_SECRET` environment variables within the `.env` file respectively.

4. Review and follow the [Creating a Webex Bot](https://developer.webex.com/docs/bots#creating-a-webex-bot) guide to create a Webex Bot.
    - Take note of your Bot ID and Bot access token. Assign these values to the `WEBEX_BOT_ID` and `WEBEX_BOT_TOKEN` environment variables within the `.env` file respectively. 
    - You will also need to add this bot to all shared device that you wish to use. To do this, sign in to Webex Control Hub (admin.webex.com) > Workspaces > Your Workspace Name > Edit API Access > Search for your bot and grant 'Full Access'.

5. Install Composer dependencies for the application.
   ```
   docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
   ```

6. Start the Docker development environment via [Laravel Sail](https://laravel.com/docs/8.x/sail):
   ```
   ./vendor/bin/sail up -d
   ```

7. Initialize the database for the application.
   ```
   ./vendor/bin/sail php artisan migrate:fresh
   ```

8. Install NPM dependencies for the application.
   ```
   ./vendor/bin/sail npm install
   ```

9. Run [Laravel Mix](https://laravel.com/docs/8.x/mix)  
   When you run this command, the application's CSS and JavaScript assets will be compiled and placed in the application's public directory:
   ```
   ./vendor/bin/sail npm run dev
   ```

10. Run the Scheduler locally  
    This command will run in the foreground and invoke the scheduler every minute until you terminate the command. In a new terminal window:
    ```
    ./vendor/bin/sail php artisan schedule:work
    ```

11. Run the Queue Worker  
    Start a queue worker and process new jobs as they are pushed onto the queue. This command will continue to run until it is manually stopped or you close your terminal. In a new terminal window:
    ```
    ./vendor/bin/sail php artisan queue:work
    ```

Lastly, navigate to `http://localhost` in your browser to complete the setup (you will be asked to login to Azure) by mapping devices to calendar accounts.


## Demo

A video where I demo this PoC is available on YouTube — https://www.youtube.com/watch?v=&t=s.


## Support

Please reach out to the WXSD team at [wxsd@external.cisco.com](mailto:wxsd@external.cisco.com?cc=ashessin@cisco.com&subject=Early%20One%20Button%20to%20Push) or contact me on Webex (ashessin@cisco.com).
