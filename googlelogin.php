
<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

class PlgSystemGoogleLogin extends CMSPlugin
{
    protected $app;

    public function onAfterInitialise()
    {
        $input = Factory::getApplication()->input;

        // Controlla se il parametro 'google-login' è presente
        if ($input->get('google-login', false)) {
            $this->handleGoogleLogin();
        }

        // Controlla se il parametro 'google-logout' è presente
        if ($input->get('google-logout', false)) {
            $this->handleGoogleLogout();
        }

        // Controlla se il parametro 'google-check' è presente
        if ($input->get('google-check', false)) {
            include 'check.php';
        }


    }

    private function handleGoogleLogin()
    {

       $google_oauth_redirect_uri = JURI::base() . 'index.php?google-login=1';
        $google_oauth_client_id = $this->params->get('clientid');
        $google_oauth_client_secret = $this->params->get('clientsecret');
       // $redirectpage = $this->params->get('redirectpage');
        $google_oauth_version = 'v3';

        if (isset($_GET['code'])) {
            // Simile al codice che hai fornito: scambio di token e autenticazione
            $params = [
                'code' => $_GET['code'],
                'client_id' => $google_oauth_client_id,
                'client_secret' => $google_oauth_client_secret,
                'redirect_uri' => $google_oauth_redirect_uri,
                'grant_type' => 'authorization_code'
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, true);

            if (isset($response['access_token'])) {
                // Ottieni i dati dell'utente
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/' . $google_oauth_version . '/userinfo');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
                $profile = curl_exec($ch);
                curl_close($ch);
                $profile = json_decode($profile, true);

               if (isset($profile['email'])) {
             //       $this->loginOrRegisterUser($profile);
                   $google_name_parts = [];
                   $google_name_parts[] = isset($profile['given_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['given_name']) : '';
                   $google_name_parts[] = isset($profile['family_name']) ? preg_replace('/[^a-zA-Z0-9]/s', '', $profile['family_name']) : '';
                   // Authenticate the user
                   session_regenerate_id();
                   $_SESSION['google_loggedin'] = TRUE;
                   $_SESSION['google_email'] = $profile['email'];
                   $_SESSION['google_name'] = implode(' ', $google_name_parts);
                   $_SESSION['google_picture'] = isset($profile['picture']) ? $profile['picture'] : '';
                   // Redirect to profile page
                   // header('Location: profile.php');
                   header('Location: index.php?google-check=1');
                   exit;
                }
            }
        } else {
            // Redirige l'utente alla pagina di login di Google
            $params = [
                'response_type' => 'code',
                'client_id' => $google_oauth_client_id,
                'redirect_uri' => $google_oauth_redirect_uri,
                'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                'access_type' => 'offline',
                'prompt' => 'consent'
            ];
            header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
            exit;
        }
    }
    private function handleGoogleLogout()
    {
        // Initialize the session
        session_start();
        // Destroy the session
        session_destroy();
        // Redirect to the login page
        $this->app->redirect('https://www.galileivr.edu.it/it/accesso');
    }


}

?>


