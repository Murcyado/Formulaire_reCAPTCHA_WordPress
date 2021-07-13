<?php
/*
 * Plugin Name: EISGE reCAPTCHA 2.0
 * Plugin URI: 
 * Description: Un Plugin formulaire 2.0
 * Version: 1.0
 * Author: Murcyado
 * Author URI: 
*/
add_action('admin_menu', 'add_menu_formulaire');
function add_menu_formulaire()
{
    add_menu_page('Menu formulaire', 'Menu formulaire', 'administrator', 'api-plugin', 'onglet_formulaire');
}
function onglet_formulaire(){
 // On regarde si la méthode de requête POST est bien utilisée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

    // On construit la requête POST
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6Ldc89sZAAAAAFqgzZQPk8Z-VLh9jEjuag7yCFZh';
    $recaptcha_response = $_POST['recaptcha_response'];

    // Faire et décoder la requête POST:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $data = json_decode($recaptcha);

    // La requête agit en fonction du score et du succès obtenu:
    if ($data->score >= 0.5 && $data->success == true) {
        echo "Vous êtes un humain!</br>";
		var_dump ($data);
    } else {
        // Si la requête n'est pas vérifiée ou alors si il y a une erreur (ou alors c'est vraiment un robot qui fait la requête)
		echo "Vous êtes un robot!</br>";
		var_dump($data);
    }
}else{ // Si la méthode POST ne fonctionne pas où n'est pas utilisée
	http_response_code(405);
	echo "Méthode non autorisée ou non activée"; 
}
 ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google reCAPTCHA v3</title>
    <script src="https://www.google.com/recaptcha/api.js?render=6Ldc89sZAAAAACY-t9GaVwKXpfM4o9CEwJZDk8Xq"></script>
    <script type ="text/javascript">
        grecaptcha.ready(function () {
            grecaptcha.execute('6Ldc89sZAAAAACY-t9GaVwKXpfM4o9CEwJZDk8Xq', { action: 'homepage'}).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
				
            });
        });
    </script>
</head>
<body>
        <form method="POST">
            <center><h1 class="title">
                Formulaire reCAPTCHA v3
            </h1></center>
            <center><label class="label">Votre nom : </label></center>
			<br>
            <center><input type="text" name="nom"  class="input" placeholder="Votre nom : " required /></center> <!--On entre notre nom-->
			<br>
            <center><label class="label">Votre adresse mail : </label></center>
			<br>
            <center><input type="email" name="email" class="input" placeholder="Votre adresse mail : " required /></center><!--On entre notre adresse mail (entrer n'importe quelle adresse mail)-->
			<br>
			<center><button type="submit" class="button is-link"id="valider">Valider</button></center><!-- A noter que vous êtes obliger de remplir les zone de texte "nom" et "email" pour l'activer -->

            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
       </form>
</body>
</html>
<?php
}