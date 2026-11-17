<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion GSB</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/login.css" />
    <link rel="stylesheet" href="<?php echo base_url('/public/image/logo final.png'); ?>" />
</head>

<body>
    <div id="wrap">
        <header class="header">
            <nav class="nav">
                <a href="#wrap" id="open">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        x="0px" y="0px" width="34px" height="27px" viewBox="0 0 34 27" enable-background="new 0 0 34 27"
                        xml:space="preserve">
                        <rect fill="#FFFFFF" width="34" height="4" />
                        <rect y="11" fill="#FFFFFF" width="34" height="4" />
                        <rect y="23" fill="#FFFFFF" width="34" height="4" />
                    </svg>
                </a>
                <a href="#" id="close">×</a>
                <h1><a href="getdata?action=index">Contexte GSB</a></h1>
                <a href="getdata?action=historique">Notre Histoire</a>
                <a href="getdata?action=search">Recherche et Innovation</a>
                <a href="getdata?action=apropos">À propos</a>
                <a href="getdata?action=connection">Connexion</a>
                <a href="getdata?action=contact">Contact</a>
                <a href="getdata?action=legalnotice">Mentions légales</a>
            </nav>
        </header>
        <main class="main">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <h1>Connexion GSB</h1>
                        <p>Accédez à votre espace personnel</p>
                    </div>

                    <div id="errorMessage" class="error-message" style="display: block; margin-bottom: 10px; color: #900; background:#fee; border:1px solid #f99; padding:8px; display:none;" ></div>

                    <form id="loginForm" class="login-form" method="POST" action="postdata">
                        <div class="form-group">
                            <label for="username">
                                Nom d'utilisateur
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                placeholder="ex: lvillachane"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password">
                                Mot de passe
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                placeholder="ex: jux7g"
                                required
                            >
                        </div>

                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Se souvenir de moi</label>
                        </div>

                        <button type="submit" class="login-btn">Se connecter</button>
                    </form>

                    <div class="login-footer">
                        <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                        <p style="font-size: 12px; color: var(--gray); margin-top: 15px;">
                            Compte de test : Visiteur<br>
                            Login: <strong>lvillachane</strong><br>
                            MDP: <strong>jux7g</strong>
                        <p>

                            Pour créer un compte, veuillez contacter l'administrateur du système. <br>
                            Pour des raisons de sécurité, l'inscription en ligne n'est pas disponible.<br>
                            * Les données de connexion sont fictives et utilisées uniquement à des fins de démonstration.
                        </p>
                    </div>
                </div>


            </div>
        </main>
    </div>
    <div class="overlay"></div>
    </header>
</body>