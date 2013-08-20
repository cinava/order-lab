<?php

/* OlegOrderformBundle:Default:index.html.twig */
class __TwigTemplate_2f3afd1a0caac798de47c8d2f18cdbd7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("OlegOrderformBundle::Default/base.html.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'textarea_widget' => array($this, 'block_textarea_widget'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "OlegOrderformBundle::Default/base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 7
    public function block_content($context, array $blocks = array())
    {
        echo " 
    
    <h1>All Slide List</h1>

    <div class=\"btn_margin_top\">
        <form method=\"get\" action=\"";
        // line 12
        echo $this->env->getExtension('routing')->getPath("order_new");
        echo "\">
            <button type=\"submit\" class=\"btn btn-primary btn-success\">New Order</button>
        </form>
    </div>

    <table class=\"table table-hover table-condensed\">
        <thead>
            <tr>
                <th>Id</th>
                <th>Accession</th>
                <th>Stain</th>
                <th>Mag</th>
                <th>Diagnosis</th>
";
        // line 27
        echo "            </tr>
        </thead>
        <tbody>
        ";
        // line 30
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["slides"]) ? $context["slides"] : null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["slide"]) {
            // line 31
            echo "            <tr>
                <td><a href=\"";
            // line 32
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("order_show", array("id" => $this->getAttribute((isset($context["slide"]) ? $context["slide"] : null), "id"))), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["slide"]) ? $context["slide"] : null), "id"), "html", null, true);
            echo "</a></td>               
                <td>";
            // line 33
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["slide"]) ? $context["slide"] : null), "accession"), "html", null, true);
            echo "</td>
                <td>";
            // line 34
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["slide"]) ? $context["slide"] : null), "stain"), "html", null, true);
            echo "</td>
                <td>";
            // line 35
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["slide"]) ? $context["slide"] : null), "mag"), "html", null, true);
            echo "</td>
";
            // line 37
            echo "                <td>
                ";
            // line 38
            $this->displayBlock('textarea_widget', $context, $blocks);
            // line 45
            echo "                </td>
                              
                <td>
                    <a class=\"btn btn-small btn-info\" href=\"";
            // line 48
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("order_show", array("id" => $this->getAttribute((isset($context["slide"]) ? $context["slide"] : null), "id"))), "html", null, true);
            echo "\">Show</a>
                </td>
            </tr>
        ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['slide'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 52
        echo "        </tbody>
    </table>

";
    }

    // line 38
    public function block_textarea_widget($context, array $blocks = array())
    {
        // line 39
        echo "                ";
        ob_start();
        // line 40
        echo "                    <div class=\"textarea_widget\">
                        <textarea ";
        // line 41
        $this->displayBlock("widget_attributes", $context, $blocks);
        echo ">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["slide"]) ? $context["slide"] : null), "diagnosis"), "html", null, true);
        echo "</textarea>
                    </div>
                ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
        // line 44
        echo "                ";
    }

    public function getTemplateName()
    {
        return "OlegOrderformBundle:Default:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 45,  100 => 38,  113 => 39,  110 => 38,  97 => 37,  1357 => 388,  1348 => 387,  1346 => 386,  1343 => 385,  1327 => 381,  1320 => 380,  1318 => 379,  1315 => 378,  1292 => 374,  1267 => 373,  1265 => 372,  1262 => 371,  1250 => 366,  1245 => 365,  1243 => 364,  1240 => 363,  1231 => 357,  1225 => 355,  1222 => 354,  1217 => 353,  1215 => 352,  1212 => 351,  1205 => 346,  1196 => 344,  1192 => 343,  1189 => 342,  1186 => 341,  1184 => 340,  1181 => 339,  1173 => 335,  1171 => 334,  1168 => 333,  1162 => 329,  1156 => 327,  1153 => 326,  1151 => 325,  1148 => 324,  1139 => 319,  1137 => 318,  1114 => 317,  1111 => 316,  1108 => 315,  1105 => 314,  1102 => 313,  1099 => 312,  1096 => 311,  1094 => 310,  1091 => 309,  1084 => 305,  1080 => 304,  1075 => 303,  1073 => 302,  1070 => 301,  1063 => 296,  1060 => 295,  1052 => 290,  1049 => 289,  1047 => 288,  1044 => 287,  1036 => 282,  1032 => 281,  1028 => 280,  1025 => 279,  1023 => 278,  1020 => 277,  1012 => 273,  1010 => 269,  1008 => 268,  1005 => 267,  1000 => 263,  978 => 258,  975 => 257,  972 => 256,  969 => 255,  966 => 254,  963 => 253,  960 => 252,  957 => 251,  954 => 250,  951 => 249,  948 => 248,  946 => 247,  943 => 246,  935 => 240,  932 => 239,  930 => 238,  927 => 237,  919 => 233,  916 => 232,  914 => 231,  911 => 230,  899 => 226,  896 => 225,  893 => 224,  890 => 223,  888 => 222,  885 => 221,  877 => 217,  874 => 216,  872 => 215,  869 => 214,  861 => 210,  858 => 209,  856 => 208,  853 => 207,  845 => 203,  842 => 202,  840 => 201,  837 => 200,  829 => 196,  826 => 195,  824 => 194,  821 => 193,  813 => 189,  810 => 188,  808 => 187,  805 => 186,  797 => 182,  794 => 181,  792 => 180,  789 => 179,  781 => 175,  779 => 174,  776 => 173,  768 => 169,  765 => 168,  763 => 167,  760 => 166,  752 => 162,  749 => 161,  747 => 160,  745 => 159,  742 => 158,  735 => 153,  725 => 152,  720 => 151,  717 => 150,  711 => 148,  708 => 147,  706 => 146,  703 => 145,  695 => 139,  693 => 138,  692 => 137,  691 => 136,  690 => 135,  685 => 134,  679 => 132,  676 => 131,  674 => 130,  671 => 129,  662 => 123,  658 => 122,  654 => 121,  650 => 120,  645 => 119,  639 => 117,  636 => 116,  634 => 115,  631 => 114,  615 => 110,  613 => 109,  610 => 108,  594 => 104,  592 => 103,  589 => 102,  572 => 98,  560 => 96,  553 => 93,  551 => 92,  546 => 91,  543 => 90,  525 => 89,  523 => 88,  520 => 87,  511 => 82,  508 => 81,  505 => 80,  499 => 78,  497 => 77,  492 => 76,  489 => 75,  486 => 74,  471 => 72,  459 => 69,  456 => 68,  450 => 64,  442 => 62,  433 => 60,  428 => 59,  426 => 58,  414 => 52,  408 => 50,  405 => 49,  403 => 48,  400 => 47,  390 => 43,  388 => 42,  385 => 41,  377 => 37,  371 => 35,  366 => 33,  363 => 32,  350 => 26,  344 => 24,  342 => 23,  335 => 21,  332 => 20,  316 => 16,  313 => 15,  311 => 14,  308 => 13,  299 => 8,  293 => 6,  290 => 5,  281 => 385,  271 => 371,  266 => 363,  263 => 362,  260 => 360,  255 => 350,  253 => 339,  250 => 338,  248 => 333,  245 => 332,  240 => 323,  238 => 309,  233 => 301,  230 => 300,  227 => 298,  217 => 286,  215 => 277,  212 => 276,  210 => 267,  207 => 266,  204 => 264,  202 => 263,  197 => 246,  194 => 245,  191 => 243,  186 => 236,  184 => 230,  181 => 229,  179 => 221,  174 => 214,  161 => 199,  146 => 178,  104 => 87,  74 => 20,  34 => 5,  83 => 22,  152 => 49,  145 => 46,  131 => 157,  129 => 145,  124 => 129,  65 => 13,  120 => 41,  20 => 1,  90 => 32,  76 => 31,  291 => 61,  288 => 4,  279 => 43,  276 => 378,  273 => 377,  262 => 28,  257 => 27,  246 => 80,  243 => 324,  225 => 295,  222 => 294,  218 => 72,  180 => 71,  172 => 63,  170 => 60,  159 => 193,  150 => 48,  134 => 158,  81 => 40,  63 => 12,  96 => 67,  77 => 15,  58 => 19,  52 => 18,  59 => 30,  53 => 18,  23 => 2,  480 => 162,  474 => 161,  469 => 71,  461 => 70,  457 => 153,  453 => 151,  444 => 149,  440 => 148,  437 => 61,  435 => 146,  430 => 144,  427 => 143,  423 => 57,  413 => 134,  409 => 132,  407 => 131,  402 => 130,  398 => 129,  393 => 126,  387 => 122,  384 => 121,  381 => 120,  379 => 119,  374 => 36,  368 => 34,  365 => 111,  362 => 110,  360 => 109,  355 => 27,  341 => 105,  337 => 22,  322 => 101,  314 => 99,  312 => 98,  309 => 97,  305 => 95,  298 => 91,  294 => 90,  285 => 3,  283 => 55,  278 => 384,  268 => 370,  264 => 84,  258 => 351,  252 => 80,  247 => 78,  241 => 77,  235 => 308,  229 => 78,  224 => 71,  220 => 287,  214 => 69,  208 => 68,  169 => 207,  143 => 34,  140 => 55,  132 => 38,  128 => 49,  119 => 114,  111 => 107,  107 => 48,  71 => 19,  177 => 65,  165 => 64,  160 => 61,  139 => 166,  135 => 39,  126 => 44,  114 => 108,  84 => 41,  70 => 13,  67 => 12,  61 => 2,  47 => 15,  38 => 12,  94 => 57,  89 => 34,  85 => 33,  79 => 32,  75 => 14,  68 => 14,  56 => 10,  50 => 5,  29 => 7,  87 => 33,  72 => 31,  55 => 15,  21 => 4,  26 => 3,  98 => 24,  93 => 35,  88 => 6,  78 => 19,  46 => 7,  27 => 4,  40 => 8,  44 => 9,  35 => 3,  31 => 17,  43 => 6,  41 => 38,  28 => 16,  201 => 92,  196 => 90,  183 => 70,  171 => 213,  166 => 206,  163 => 70,  158 => 50,  156 => 192,  151 => 185,  142 => 59,  138 => 40,  136 => 165,  123 => 31,  121 => 128,  117 => 25,  115 => 40,  105 => 40,  101 => 32,  91 => 56,  69 => 13,  66 => 12,  62 => 16,  49 => 8,  24 => 5,  32 => 5,  25 => 3,  22 => 2,  19 => 1,  209 => 82,  203 => 78,  199 => 262,  193 => 73,  189 => 237,  187 => 84,  182 => 66,  176 => 220,  173 => 74,  168 => 66,  164 => 200,  162 => 55,  154 => 186,  149 => 44,  147 => 50,  144 => 173,  141 => 41,  133 => 48,  130 => 41,  125 => 52,  122 => 42,  116 => 113,  112 => 43,  109 => 102,  106 => 101,  103 => 26,  99 => 68,  95 => 29,  92 => 28,  86 => 46,  82 => 31,  80 => 19,  73 => 17,  64 => 11,  60 => 10,  57 => 19,  54 => 27,  51 => 9,  48 => 4,  45 => 3,  42 => 8,  39 => 6,  36 => 28,  33 => 27,  30 => 1,);
    }
}
