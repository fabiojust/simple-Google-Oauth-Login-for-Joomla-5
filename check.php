<?php



use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;

// Verifica se l'utente è loggato
$user = Factory::getUser();
if (!$user->guest) {
    return; // Interrompe l'esecuzione del plugin se l'utente è loggato
}



// Carica il database di Joomla
$db = JFactory::getDbo();
$query = $db->getQuery(true);

$session = Factory::getSession();

// Retrieve session variables
$google_loggedin = $_SESSION['google_loggedin'] ?? null;
$google_email = $_SESSION['google_email'] ?? null;
$google_name = $_SESSION['google_name'] ?? null;
$google_picture = $_SESSION['google_picture'] ?? null;

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

        // Reindirizza l'utente a una pagina specifica
        $app->redirect(''); // Cambia con la tua destinazione
    } else {
        // Utente non trovato
        // 'Utente non trovato per questa email.';
        Factory::getApplication()->enqueueMessage('Utente non trovato per questa email.', 'error');
    }
}


// Esempio di utilizzo
$emailToCheck = $google_email;
if (checkUserByEmail($emailToCheck)) {
    autoLoginByEmail($emailToCheck);

} else {
    return true;
}

?>
