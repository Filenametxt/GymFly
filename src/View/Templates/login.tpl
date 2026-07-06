<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Login</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.4">
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
                <div class="navbar-items is-flex is-align-items-center" style="height: 100%;">
                    <a class="navbar-item is-tab px-4 py-2 mr-2" href="registrazione">
                        <span>SIGN IN</span>
                        <span class="icon is-small ml-2">
                            <i class="fas fa-user-plus"></i>
                        </span>
                    </a>
                    <a class="navbar-item is-tab is-active-login px-4 py-2" href="login">
                        <span>LOG IN</span>
                        <span class="icon is-small ml-2">
                            <i class="fas fa-user"></i>
                        </span>
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
                            
                            <div class="avatar-container">{if isset($userAvatar) && $userAvatar != ''}<img src="{$userAvatar}" alt="Profile Picture">{else}<i class="fas fa-user-circle"></i>{/if}</div>

                            <h3 class="title is-3 has-text-centered mb-5" style="color: #AFAFE2; letter-spacing: 2px;">LOG IN</h3>

                            {if isset($errorMessage) && $errorMessage != ''}
                                <div class="notification is-danger is-light py-2 px-4 mb-4 is-size-7">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> {$errorMessage}
                                </div>
                            {/if}

                            <form action="login" method="POST">

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
                                            <a href="#" class="is-size-7 has-text-grey-dark">Forgot password?</a>
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