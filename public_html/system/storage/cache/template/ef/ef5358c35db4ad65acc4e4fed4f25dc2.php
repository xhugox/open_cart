<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* default/template/common/maintenance.twig */
class __TwigTemplate_0b2efb70fcf4fc9cc7befc7ac688f15d extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <title>Skrynelė.lt - Mes tuoj grįšime</title>
        <!-- CSS -->
        <link rel=\"stylesheet\" href=\"http://fonts.googleapis.com/css?family=Lobster\">
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Lato:400,700'>
      <link rel=\"stylesheet\" href=\"/assets/bootstrap/css/bootstrap.min.css\">
      <link rel=\"stylesheet\" href=\"/assets/font-awesome/css/font-awesome.min.css\">
      <link rel=\"stylesheet\" href=\"/assets/css/style.css\">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src=\"https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js\"></script>
            <script src=\"https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js\"></script>
        <![endif]-->
      <link rel=\"shortcut icon\" href=\"/assets/ico/favicon.png\">
    </head>
    <body>
        <!-- Header -->
        <div class=\"container\">
            <div class=\"row header\">
                <div class=\"col-sm-4 logo\">
                    <h1><a href=\"https://skrynelė.lt/\">Skrynelė.lt</a> <span><h4>© MB Marga skrynelė</h4></span></h1>
                </div>
                <div class=\"col-sm-8 call-us\">
                    <p>Tel: <span>+370 630 42530</span></p>
                </div>
            </div>
        </div>
        <div class=\"coming-soon\">
            <div class=\"inner-bg\">
                <div class=\"container\">
                    <div class=\"row\">
                        <div class=\"col-sm-12\">
                            <h2>Mes tuoj grįšime</h2>
                            <p>Vykdomas serverio atnaujinimas. Sekite naujienas!</p>
                            <div class=\"timer\">
                                <div class=\"days-wrapper\">
                                    <span class=\"days\"></span> <br>diena
                                </div>
                                <div class=\"hours-wrapper\">
                                    <span class=\"hours\"></span> <br>valandos
                                </div>
                                <div class=\"minutes-wrapper\">
                                    <span class=\"minutes\"></span> <br>minutės
                                </div>
                                <div class=\"seconds-wrapper\">
                                    <span class=\"seconds\"></span> <br>sekundės
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class=\"container\">
            <div class=\"row\">
                <div class=\"col-sm-12 subscribe\">
                    <h3>Jei norite gauti pranešimą</h3>
                    <p>apie atnaujinimo pabaigą, įrašykite savo el. pašto adresą:</p>                    
                  <form class=\"form-inline\" role=\"form\" action=\"/assets/subscribe.php\" method=\"post\">
                    \t<div class=\"form-group\">
                    \t\t<label class=\"sr-only\" for=\"subscribe-email\">elektroninio pašto adresas</label>
                        \t<input type=\"text\" name=\"email\" placeholder=\"Įveskite el. pašto adresą...\" class=\"subscribe-email form-control\" id=\"subscribe-email\">
                        </div>
                        <button type=\"submit\" class=\"btn\">Siųsti</button>
                    </form>
                    <div class=\"success-message\"></div>
                    <div class=\"error-message\"></div>
                </div>
            </div>
        </div>
      <script src=\"/assets/js/jquery-1.11.1.min.js\"></script>
      <script src=\"/assets/bootstrap/js/bootstrap.min.js\"></script>
      <script src=\"/assets/js/jquery.backstretch.min.js\"></script>
      <script src=\"/assets/js/jquery.countdown.min.js\"></script>
      <script src=\"/assets/js/scripts.js\"></script>
        <!--[if lt IE 10]>
            <script src=\"/assets/js/placeholder.js\"></script>
        <![endif]-->
    </body>
</html>";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "default/template/common/maintenance.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "default/template/common/maintenance.twig", "");
    }
}
