<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* modules/custom/semantic_404/templates/semantic-404-card.html.twig */
class __TwigTemplate_d7a90949c35bfb334ebe07d3d1db334b extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 13
        yield "<div class=\"s404-card\" role=\"complementary\" aria-label=\"AI Page Suggestion\">

  <div class=\"s404-card__header\">
    <span class=\"s404-card__ai-badge\">✨ Powered by AI</span>
    <span class=\"s404-card__confidence-badge
      ";
        // line 18
        if ((($context["score"] ?? null) >= 90)) {
            yield "s404-card__confidence-badge--high";
        } elseif ((($context["score"] ?? null) >= 75)) {
            yield "s404-card__confidence-badge--medium";
        } else {
            yield "s404-card__confidence-badge--low";
        }
        yield "\">
      ";
        // line 19
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["score"] ?? null), "html", null, true);
        yield "% match
    </span>
  </div>

  <div class=\"s404-card__body\">
    <p class=\"s404-card__hint\">";
        // line 24
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("We couldn't find that page, but this might help:"));
        yield "</p>
    <h2 class=\"s404-card__title\">";
        // line 25
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title"] ?? null), "html", null, true);
        yield "</h2>
    <p class=\"s404-card__snippet\">";
        // line 26
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["snippet"] ?? null), "html", null, true);
        yield "</p>
  </div>

  <div class=\"s404-card__footer\">
    <a href=\"";
        // line 30
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["url"] ?? null), "html", null, true);
        yield "\" class=\"s404-card__cta\" aria-label=\"";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Go to %title", ["%title" => ($context["title"] ?? null)]));
        yield "\">
      ";
        // line 31
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Take me there"));
        yield " &rarr;
    </a>
  </div>

</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["score", "title", "snippet", "url"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/semantic_404/templates/semantic-404-card.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  90 => 31,  84 => 30,  77 => 26,  73 => 25,  69 => 24,  61 => 19,  51 => 18,  44 => 13,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/semantic_404/templates/semantic-404-card.html.twig", "C:\\Users\\Soham Lad\\OneDrive\\Desktop\\drpy\\web\\modules\\custom\\semantic_404\\templates\\semantic-404-card.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 18];
        static $filters = ["escape" => 19, "t" => 24];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 't'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
