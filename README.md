# simple-Google-Oauth-Login-for-Joomla-5
A simple plugin for setting Oauth Google Login for Joomla 5. 
Nb. The plugin doesn't work with new users (not yet).

# Prerequisites
You must have only:
- Cliend ID
- Secret Key
From Google API Console Project

...and a Joomla 5 site, obviously.

## 1. Download the project in .zip
Rename the zip file "googlelogin.zip"

## 2. Install the plugin
Install plugin from file in your admin site page.

## 3. Activate the plugin
Go to plugin page, in the backend, and search the Google Login plugin. Write your client ID and your secret key (if you don't know what they are, read the first part of this good tutorial: https://codeshack.io/implement-google-login-php/ ). 
**Nb. In the redirect URI of Google Cloud Project you have to insert https://*yoursite*/index.php?google-login=1

Then, activate the plugin.

## 4. Insert the Google Login Button
In your template folder, find the template of your login page (templates/yourtemplate/html/com_users/logi/dafault_login.php) and add the link for Google Login by adding this code:
```
<a class="btn btn-primary" href="index.php?google-login=1"> Login with Google </a>
```
At this point, if you have done everything right, the Google Login work. 
##  5. Redirect after login
The plugin send the user at the home page of your site. If you want to change that, go to check.php and insert the page link in the line
```
 // Reindirizza l'utente a una pagina specifica
 $app->redirect('https://www.yoursite/page'); // Cambia con la tua destinazione
```
