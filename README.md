Early One Button to Push
========================
**Customize the timing of OBTP on your shared Webex devices.**

Conventionally, you can configure the [OBTP](https://help.webex.com/en-us/article/gmi931/Overview-of-One-Button-to-Push-(OBTP)) to show up 5 minutes before the meeting time at an organizational level (via. Webex Control Hub settings).
However, this proof-of-concept application lets you set any custom value (in seconds) for the OBTP to appear, that too at a device level. For example:
> Your organization has a shared Webex device in a large conference room. You find that 5 minutes isn't sufficient for your team to settle in and start the meeting. With this PoC, you can configure the OBTP to appear 30 minutes before the meeting start time on that specific device. This ensures everyone has ample time to join and prepare for the meeting, resulting in a smoother and more efficient start.

The target audience for this PoC are individuals or teams who want to have greater control over the timing of the OBTP. This PoC support any type of room booking, regardless of what platform ([Webex](https://www.webex.com/), [Google Meet](https://workspace.google.com/products/meet/), [Microsoft Teams](https://www.microsoft.com/en-us/microsoft-teams), [Zoom](https://zoom.us/)) they are tied to.


<p align="center">
   <a href="https://app.vidcast.io/share/cef825db-f4e2-42b5-a686-25b5e14ea4e3" target="_blank">
       <img src="https://github.com/wxsd-sales/early-one-button-to-push/assets/6129517/753746c3-6236-49af-a10a-677a1498afeb" alt="early-one-button-to-push-demo"/>
    </a>
</p>


<!-- ⛔️ MD-MAGIC-EXAMPLE:START (TOC:collapse=true&collapseText=Click to expand) -->
<details>
<summary>Table of Contents (click to expand)</summary>

  * [Overview](#overview)
  * [Setup](#setup)
  * [Demo](#demo)
  * [Disclaimer](#disclaimer)
  * [License](#license)
  * [Support](#support)

</details>
<!-- ⛔️ MD-MAGIC-EXAMPLE:END -->

## Overview
At it's core, the application is a collection of background processes that run on a predefined schedule.

These processes, collectively, retrieve a user's calendar events and create corresponding 
bookings for those on devices that are mapped to the user.

The front end serves as a way to "map" or "unmap" calendar accounts to shared devices. 
All bookings are done via a bot account that has admin privileges to the shared devices (on Webex Control Hub).


## Setup

These instructions assume that you have:
- Administrator access to an Azure AD Tenant and Webex Control Hub.
- Set the target devices as [shared devices](https://help.webex.com/en-US/article/1mqb9cb/Add-Shared-Devices-and-Services-to-a-Workspace).
- [Docker installed](https://docs.docker.com/engine/install/) and running on a Windows (via WSL2), macOS, or Linux machine.

Open a new terminal window and follow the instructions below to setup the project locally for 
development/demo.

1. Clone this repository and change directory:
   ```
   git clone https://github.com/WXSD-Sales/early-one-button-to-push && cd early-one-button-to-push
   ```

2. Copy `.env.example` file as `.env` (you may also change the database credentials within this new file):
   ```
   cp .env.example .env
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
    - Take note of your [Azure Tenant ID](https://docs.microsoft.com/en-us/azure/active-directory/fundamentals/active-directory-how-to-find-tenant), 
      Application ID and, Client Secret. Assign these values to the `AZURE_TENANT_ID`, 
      `AZURE_CLIENT_ID`, and `AZURE_CLIENT_SECRET` environment variables within the `.env` 
      file respectively.

4. Review and follow the [Creating a Webex Bot](https://developer.webex.com/docs/bots#creating-a-webex-bot) guide to create a Webex Bot.
    - Take note of your Bot ID and Bot access token. Assign these values to the `WEBEX_BOT_ID` 
      and `WEBEX_BOT_TOKEN` environment variables within the `.env` file respectively. 
    - You will also need to add this bot to all shared device that you wish to use. To do this, 
      sign in to Webex Control Hub (admin.webex.com) > Workspaces > Your Workspace Name > Edit 
      API Access > Search for your bot and grant 'Full Access'.

5. [Install Composer dependencies for the application](https://laravel.com/docs/9.x/sail#installing-composer-dependencies-for-existing-projects):
   ```
   docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
   ```

6. Start the Docker development environment via [Laravel Sail](https://laravel.com/docs/9.x/sail):
   ```
   ./vendor/bin/sail up -d
   ```

7. Generate the [application key](https://laravel.com/docs/9.x/encryption#configuration):
   ```
   ./vendor/bin/sail php artisan key:generate
   ```

8. Initialize the [database for the application](https://laravel.com/docs/9.x/migrations#drop-all-tables-migrate):
   ```
   ./vendor/bin/sail php artisan migrate:fresh
   ```

9. Install NPM dependencies for the application:
   ```
   ./vendor/bin/sail npm install
   ```

10. Run [Laravel Mix](https://laravel.com/docs/9.x/mix#running-mix):
    ```
    ./vendor/bin/sail npm run dev
    ```

Lastly, navigate to `http://localhost` in your browser to complete the setup by mapping devices 
to calendar accounts. To stop, execute `./vendor/bin/sail down` on the terminal.


## Demo

A video where I demo this PoC is available on Vidcast — [https://app.vidcast.io/share/cef825db-f4e2-42b5-a686-25b5e14ea4e3](https://app.vidcast.io/share/cef825db-f4e2-42b5-a686-25b5e14ea4e3)
and Youtube — [https://youtu.be/saAgLuW8qG4&t=60s](https://youtu.be/saAgLuW8qG4&t=60s).

## Disclaimer

Everything included in this repository is for demo and Proof of Concept (PoC) purposes only. Use of the PoC is solely
at your own risk. This project may contain links to external content, which we do not warrant, endorse, or assume
liability for. This project is for Cisco Webex use-case, but is not official Cisco Webex branded project.

## License

[MIT](./LICENSE)

## Support

Please reach out to the WXSD team at [wxsd@external.cisco.com](mailto:wxsd@external.cisco.com?cc=ashessin@cisco.com&subject=Early%20One%20Button%20to%20Push) or contact me on Webex (ashessin@cisco.com).
