<?php
/*
 * Plugin Name: EISGE reCAPTCHA widget
 * Plugin URI:
 * Description: Un widget reCAPTCHA
 * Version: 1.0
 * Author: Murcyado
 * Author URI: 
*/
add_action('widgets_init','recaptcha_init');

function recaptcha_init(){
register_widget('recaptcha_widget');
}

class recaptcha_widget extends WP_Widget{
	
	//Construction du widget
	function recaptcha_widget(){
		$options = array(
				"classname" => "recaptcha_widget", 
				"description" => "Un widget affichant un formulaire"
		);
		$this->WP_widget("widget-recaptcha","Widget reCAPTCHA",$options); //1er paramètre = id du widget (pas donné le meme a 2 widget), 2ème paramètre = nom du widget
	}
	
	// Mise en forme
	function widget($args,$instance){
		extract($args);
		echo $before_widget;
		echo $before_title.$instance["titre"].$after_title;
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

			// On construit la requête POST
			$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = '6Ldc89sZAAAAAFqgzZQPk8Z-VLh9jEjuag7yCFZh';
			$recaptcha_response = $_POST['recaptcha_response'];

			// Faire et décoder la requête POST:
			$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
			$dataconsole = json_encode($recaptcha);
			$data = json_decode($recaptcha);

			// La requête agit en fonction du score et du succès obtenu:
			if ($data->score >= 0.5 && $data->success == true) {
				echo "Vous êtes un humain!</br><br>";
				
			} else {
				// Si la requête n'est pas vérifiée ou alors si il y a une erreur (ou alors c'est vraiment un robot qui fait la requête)
				echo "Vous êtes un robot!</br><br>";
			}
		}else{ // Si la méthode POST ne fonctionne pas où n'est pas utilisée
			http_response_code(405);
			echo "Méthode non autorisée ou non activée</br><br>"; 
		}
		?>
		<script src="https://www.google.com/recaptcha/api.js?render=6Ldc89sZAAAAACY-t9GaVwKXpfM4o9CEwJZDk8Xq"></script>
		<script type="text/javascript">
			grecaptcha.ready(function () {
            grecaptcha.execute('6Ldc89sZAAAAACY-t9GaVwKXpfM4o9CEwJZDk8Xq', { action: 'homepage'}).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
		</script>
		<p> Bonjour! Bienvenue sur mon formulaire reCAPTCHA!</p>
		<p> La réponse de Google reCAPTCHA est : </p>
		<br>
		<form method="POST">
		<label for="<?php echo $this->get_field_id("titre"); ?>">Titre : </label>
		<input value="<?php echo $instance["titre"]; ?>" name="<?php echo $this->get_field_name("titre"); ?>" id="<?php echo $this->get_field_id("titre"); ?>" type="text" disabled />
		<br>
		<label class="label">Votre nom : </label>
		<br>
        <input type="text" name="nom"  class="input" placeholder="Votre nom : " required /> <!--On entre notre nom-->
		<br>
        <label class="label">Votre adresse mail : </label>
		<br>
        <input type="email" name="email" class="input" placeholder="Votre adresse mail : " required /><!--On entre notre adresse mail (entrer n'importe quelle adresse mail)-->
		<br><br>
		<center><button type="submit" class="button is-link"id="valider">Valider</button></center>
		<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
		</form>
		<?php
		print_r("<script> console.log($dataconsole); </script>");
		echo $after_widget;
	}

	// Récupération des paramètres
	function update($new,$old){
		return $new; //pour sauvegarder
	}

	// Paramètres dans l'administration du widget
	function form($instance){
		$default = array(
			"titre" => "Widget reCAPTCHA"
		);
		$instance = wp_parse_args($instance,$default); //va compléter le 1er tableau avec les valeurs du deuxième
		?>
		<p>
			<label for="<?php echo $this->get_field_id("titre"); ?>">Titre : </label>
			<input value="<?php echo $instance["titre"]; ?>" name="<?php echo $this->get_field_name("titre"); ?>" id="<?php echo $this->get_field_id("titre"); ?>" type="text"/>
			<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
		</p>
		<?php
	}
}