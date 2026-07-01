<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {literal}
    <style>
        html, body {
            background-color: #F4F9F1; /* Sfondo principale chiaro dalla palette */
            height: 100%;
        }
        
        .hero.is-fullheight-with-navbar {
            /* Sovrapposizione di un filtro chiaro all'immagine per garantire la leggibilità */
            background: linear-gradient(rgba(244, 249, 241, 0.85), rgba(244, 249, 241, 0.85)), 
                        url('assets/img/sfondo-palestra.jpg') no-repeat center center;
            background-size: cover;
        }

        .navbar {
            background-color: #F4F9F1;
            border-bottom: 2px solid #99CDEA; /* Linea di divisione azzurra */
        }

        /* Stile del tab "Log In" attivo (rialzato come nel layout) */
        .navbar-item.is-tab.is-active-login {
            background-color: #C5E0FC;
            border-top: 2px solid #99CDEA;
            border-left: 2px solid #99CDEA;
            border-right: 2px solid #99CDEA;
            border-bottom: none !important;
            border-radius: 12px 12px 0 0;
            font-weight: bold;
        }

        /* Box centrale del Login */
        .login-box {
            background-color: #FFFFFF;
            border: 2px solid #AFAFE2;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            padding: 2.5rem;
        }

        /* Bottone Login personalizzato con il colore lilla */
        .button.is-gymfly {
            background-color: #AFAFE2;
            color: #FFFFFF;
            border-radius: 12px;
            transition: background 0.3s ease;
        }

        .button.is-gymfly:hover {
            background-color: #D0D0F5;
            color: #333333;
        }

        /* Contenitore per l'avatar del profilo (arrotondato morbido) */
        .avatar-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem auto;
            overflow: hidden;
            border-radius: 15px;
            border: 2px solid #AFAFE2;
        }
        
        .avatar-container img {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        /* Input con focus basato sui colori della palette */
        .input:focus {
            border-color: #99CDEA;
            box-shadow: 0 0 0 0.125em rgba(153, 205, 234, 0.25);
        }
    </style>
    {/literal}
</head>
<body>

    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="{$baseUrl|default:'/'}">
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly</strong>
                </a>
            </div>

            <div class="navbar-end">
                <div class="navbar-items is-flex is-align-items-flex-end" style="height: 100%;">
                    <a class="navbar-item is-tab px-4 py-2" href="{$baseUrl|default:''}/signup" style="border-bottom: none;">
                        SIGN IN
                    </a>
                    <a class="navbar-item is-tab is-active-login px-4 py-2" href="{$baseUrl|default:''}/login">
                        LOG IN
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero is-fullheight-with-navbar">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-5-tablet is-4-desktop is-4-widescreen">
                        
                        <div class="login-box">
                            
                            <div class="avatar-container">
                                <img src="{$userAvatar|default:'assets/img/default-avatar.jpg'}" alt="Profile Picture">
                            </div>

                            <h3 class="title is-3 has-text-centered mb-5" style="color: #AFAFE2; letter-spacing: 2px;">LOG IN</h3>

                            {if isset($errorMessage) && $errorMessage != ''}
                                <div class="notification is-danger is-light py-2 px-4 mb-4 is-size-7">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> {$errorMessage}
                                </div>
                            {/if}

                            <form action="{$baseUrl|default:''}/login-process" method="POST">
                                
                                <div class="field mb-4">
                                    <label class="label is-small">e-mail</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="email" name="email" placeholder="esempio@gymfly.it" value="{$typedEmail|default:''}" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mb-5">
                                    <label class="label is-small">password</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="password" name="password" placeholder="••••••••" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="level mt-6">
                                    <div class="level-left">
                                        <div class="level-item">
                                            <a href="{$baseUrl|default:''}/forgot-password" class="is-size-7 has-text-grey-dark">Forgot password?</a>
                                        </div>
                                    </div>
                                    <div class="level-right">
                                        <div class="level-item">
                                            <button type="submit" class="button is-gymfly px-5">
                                                LOGIN
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>