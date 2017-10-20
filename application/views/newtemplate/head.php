<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <!--BOOTSTRAP-->
        <script
            src="https://code.jquery.com/jquery-3.2.1.js"
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous"></script>
              <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
              <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
              <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="/css/<?= $directory ?>/font-awesome.min.css">

        <?php
        foreach (scandir($_SERVER['DOCUMENT_ROOT'] . '/css/' . $directory . '/') as $d) {
            if ($d == '.' or $d == '..') {
                continue;
            }
            ?>
            <link rel="stylesheet" href="/css/<?= $directory . '/' . $d; ?>">
            <?php
        }
        ?>

    </head>
    <body>
        <header class='header'>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="logo">
                            <div class="logo__link">
                                <a href="/" class='logo__anchor display_block'></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="contacts">
                            <div class="contacts__phone flex">
                                <div class="contacts__phone-icon"></div>
                                <div class="contacts__phonenumber">
                                    <p class='contacts__phonenumber-par'>+7(473)255-47-98</p>
                                    <p class='contacts__phonenumber-par'>+7(905)654-95-72</p>
                                    <p class='contacts__phonenumber-par'>+7(905)654-95-54</p>
                                </div>
                            </div>
                            <div class="contacts__email flex">
                                <div class="contacts__email-icon"></div>
                                <div class="contacts__email-text">
                                    <a href="mailto:info@sts2.ru">info@sts2.ru</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="social">
                            <div class="social__graphic">
                                <div class="social__graphic-header">
                                    <?= $this->lang->line('template_social_graphic_header') ?>
                                </div>
                                <div class="social__graphic-shedile">
                                    <?= $this->lang->line('template_social_graphic_shedule_workday') ?><br>
                                    <?= $this->lang->line('template_social_graphic_shedule_holiday') ?>
                                </div>
                                <div class="social__graphicSocialLink">
                                    <ul class='social__graphicSocialLinkList flex'>
                                        <li class='social__graphicSocialLinkListItem facebookIcon'></li>
                                        <li class='social__graphicSocialLinkListItem vkIcon'></li>
                                        <li class='social__graphicSocialLinkListItem twitterIcon'></li>
                                        <li class='social__graphicSocialLinkListItem okIcon'></li>
                                        <li class='social__graphicSocialLinkListItem mailruIcon'></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a class="backet display_block" href="/mybacket">
                            <div class="backet__counter">
                                <?= $this->lang->line('template_backet_title') ?>(<span><?= ($backet_status['quant']) ? $backet_status['quant'] : 0 ?></span>)
                            </div>
                            <div class="backet__summ">
                                <span><?= ($backet_status['price']) ? $backet_status['price'] : 0 ?></span>
                            </div>
                        </a>

                        <div class="loginBlock">
                            <div class="login align_center">
                                <?php
                                if (!$this->aauth->is_login()) {
                                    ?>
                                    <a href="/user/auth" class="login__link">
                                        <?= $this->lang->line('template_lk_autorize') ?>
                                    </a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="/user/lk" class="login__link">
                                        <?= $this->lang->line('template_lk') ?>
                                    </a><br>
                                    <?php
                                    if ($this->aauth->is_admin()) {
                                        ?>
                                        <a href="/panelcontrol" class="login__link">
                                            Adminpanel
                                        </a><br>
                                        <?php
                                    }
                                    ?>
                                    <a href="/user/out" class="login__link">
                                        <?= $this->lang->line('template_lk_out') ?>
                                    </a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="topMenu horizontalMenu navbar navbar-default">
            <div class="container">
                <ul class="nav nav-pills">
                    <li><a class="nav-links" href=""><?= $this->lang->line('template_navigation_catalog') ?></a></li>
                    <li><a class="nav-links"  href=""><?= $this->lang->line('template_navigation_faq') ?></a></li>
                    <li><a class="nav-links" href=""><?= $this->lang->line('template_navigation_returned') ?></a></li>
                    <li><a class="nav-links"  href=""><?= $this->lang->line('template_navigation_deliveriesAndCache') ?></a></li>
                    <li><a class="nav-links"  href=""><?= $this->lang->line('template_navigation_acessories') ?></a></li>
                    <li><a class="nav-links"  href=""><?= $this->lang->line('template_navigation_status') ?></a></li>
                    <li><a class="nav-links"  href=""><?= $this->lang->line('template_navigation_partners') ?></a></li>
                    <li><a class="nav-links"  href=""><?= $this->lang->line('template_navigation_information') ?></a></li>
                    <li><a class="nav-links"  href=""><?= $this->lang->line('template_navigation_contacts') ?></a></li>
                </ul>
            </div>
        </div>
        <main>
            <div class="container">
                <?= $content ?>
            </div>
        </main>
        <div class="bottomMenu horizontalMenu">

        </div>
        <footer></footer>
    </body>
</html>