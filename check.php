<?php



// esperimento

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;

// Carica il database di Joomla
$db = JFactory::getDbo();
$query = $db->getQuery(true);

session_start();

// Retrieve session variables
$google_loggedin = $_SESSION['google_loggedin'];
$google_email = $_SESSION['google_email'];
$google_name = $_SESSION['google_name'];
$google_picture = $_SESSION['google_picture'];

// echo '<div class="profile-picture"><img src="'.$google_picture.'" alt="" width="100" height="100"></div>';
// echo $google_name;
// echo $google_email;
// echo $google_loggedin;

function checkUserByEmail($email)
{
    // Ottieni il driver del database
    $db = Factory::getDbo();

    // Crea la query
    $query = $db->getQuery(true)
    ->select('id') // Seleziona solo l'ID (puoi aggiungere altri campi se necessario)
    ->from($db->quoteName('#__users')) // Tabella utenti di Joomla
    ->where($db->quoteName('email') . ' = ' . $db->quote($email));

    // Esegui la query
    $db->setQuery($query);
    $userId = $db->loadResult();

    // Controlla se l'utente esiste
    if ($userId) {
        return true; // Utente trovato
    }

    return false; // Utente non trovato
}





function autoLoginByEmail($email)
{
    // Ottieni l'oggetto Application
    $app = Factory::getApplication();

    // Ottieni il database
    $db = Factory::getDbo();

    // Costruisci la query per cercare l'utente corrispondente all'email
    $query = $db->getQuery(true)
    ->select('*')
    ->from($db->quoteName('#__users'))
    ->where($db->quoteName('email') . ' = ' . $db->quote($email));
    $db->setQuery($query);
    $user = $db->loadObject();

    if ($user) {
        // Simula il login manuale
        $instance = Factory::getUser($user->id); // Ottieni l'oggetto utente
        $app->setUserState('user.id', $user->id); // Imposta lo stato dell'utente nell'applicazione

        // Registra l'utente come loggato
        $session = Factory::getSession();
        $session->set('user', $instance);

        // Aggiorna la foto profilo
       // updateUserProfilePicture($user->id, $profile['picture']);
       // updateUserProfilePicture($user->id, $google_picture);

        // Reindirizza l'utente a una pagina specifica
        $app->redirect(''); // Cambia con la tua destinazione
    } else {
        // Utente non trovato
        echo 'Utente non trovato per questa email.';
    }
}





// Esempio di utilizzo
$emailToCheck = $google_email;
if (checkUserByEmail($emailToCheck)) {
    echo "L'utente con l'email $emailToCheck esiste.";
    autoLoginByEmail($emailToCheck);

} else {
    echo "L'utente con l'email $emailToCheck non esiste.";
}


?>
