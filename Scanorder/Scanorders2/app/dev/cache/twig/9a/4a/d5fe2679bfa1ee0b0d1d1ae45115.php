<?php

/* SensioDistributionBundle:Configurator:form.html.twig */
class __TwigTemplate_9a4ad5fe2679bfa1ee0b0d1d1ae45115 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("form_div_layout.html.twig");

        $this->blocks = array(
            'form_rows' => array($this, 'block_form_rows'),
            'form_row' => array($this, 'block_form_row'),
            'form_label' => array($this, 'block_form_label'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "form_div_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_form_rows($context, array $blocks = array())
    {
        // line 4
        echo "    <div class=\"symfony-form-errors\">
        ";
        // line 5
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'errors');
        echo "
    </div>
    ";
        // line 7
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 8
            echo "        ";
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["child"]) ? $context["child"] : $this->getContext($context, "child")), 'row');
            echo "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    // line 12
    public function block_form_row($context, array $blocks = array())
    {
        // line 13
        echo "    <div class=\"symfony-form-row\">
        ";
        // line 14
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'label');
        echo "
        <div class=\"symfony-form-field\">
            ";
        // line 16
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'widget');
        echo "
            <div class=\"symfony-form-errors\">
                ";
        // line 18
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'errors');
        echo "
            </div>
        </div>
    </div>
";
    }

    // line 24
    public function block_form_label($context, array $blocks = array())
    {
        // line 25
        echo "    ";
        if (twig_test_empty((isset($context["label"]) ? $context["label"] : $this->getContext($context, "label")))) {
            // line 26
            echo "        ";
            $context["label"] = $this->env->getExtension('form')->renderer->humanize((isset($context["name"]) ? $context["name"] : $this->getContext($context, "name")));
            // line 27
            echo "    ";
        }
        // line 28
        echo "    <label for=\"";
        echo twig_escape_filter($this->env, (isset($context["id"]) ? $context["id"] : $this->getContext($context, "id")), "html", null, true);
        echo "\">
        ";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans((isset($context["label"]) ? $context["label"] : $this->getContext($context, "label"))), "html", null, true);
        echo "
        ";
        // line 30
        if ((isset($context["required"]) ? $context["required"] : $this->getContext($context, "required"))) {
            // line 31
            echo "            <span class=\"symfony-form-required\" title=\"This field is required\">*</span>
        ";
        }
        // line 33
        echo "    </label>
";
    }

    public function getTemplateName()
    {
        return "SensioDistributionBundle:Configurator:form.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  295 => 275,  806 => 488,  788 => 484,  771 => 481,  686 => 466,  682 => 465,  678 => 464,  675 => 463,  673 => 462,  630 => 455,  625 => 453,  616 => 450,  565 => 414,  530 => 410,  188 => 90,  367 => 155,  358 => 151,  340 => 145,  334 => 141,  328 => 139,  296 => 121,  324 => 113,  321 => 135,  274 => 110,  265 => 105,  462 => 202,  415 => 180,  394 => 168,  380 => 158,  373 => 156,  351 => 120,  338 => 135,  329 => 131,  325 => 129,  320 => 127,  315 => 131,  303 => 106,  300 => 105,  289 => 113,  286 => 112,  270 => 102,  178 => 59,  376 => 206,  349 => 196,  330 => 187,  326 => 138,  318 => 111,  307 => 128,  275 => 105,  269 => 107,  261 => 138,  251 => 116,  232 => 88,  185 => 74,  216 => 79,  206 => 94,  127 => 35,  256 => 96,  236 => 110,  226 => 84,  195 => 93,  192 => 88,  155 => 47,  728 => 23,  722 => 20,  718 => 18,  707 => 17,  698 => 469,  680 => 306,  666 => 298,  643 => 284,  628 => 275,  622 => 271,  619 => 269,  607 => 262,  598 => 256,  584 => 248,  578 => 245,  559 => 228,  557 => 227,  555 => 226,  548 => 221,  536 => 218,  533 => 217,  531 => 216,  528 => 214,  516 => 211,  514 => 210,  500 => 204,  490 => 198,  484 => 197,  482 => 196,  476 => 192,  468 => 190,  466 => 189,  460 => 185,  458 => 184,  452 => 183,  443 => 178,  439 => 195,  431 => 189,  422 => 184,  418 => 171,  410 => 169,  401 => 172,  397 => 164,  389 => 160,  357 => 123,  353 => 149,  343 => 146,  339 => 316,  319 => 137,  310 => 133,  302 => 125,  284 => 124,  280 => 123,  254 => 125,  249 => 115,  244 => 136,  231 => 83,  223 => 115,  219 => 100,  205 => 97,  175 => 58,  167 => 84,  148 => 73,  137 => 33,  848 => 18,  834 => 12,  823 => 11,  817 => 323,  814 => 321,  803 => 487,  790 => 305,  784 => 482,  770 => 294,  759 => 286,  753 => 283,  744 => 277,  738 => 274,  732 => 270,  729 => 268,  723 => 473,  702 => 470,  694 => 468,  688 => 244,  672 => 230,  665 => 229,  660 => 295,  656 => 461,  649 => 287,  646 => 222,  641 => 218,  635 => 217,  632 => 216,  627 => 212,  621 => 452,  618 => 451,  612 => 205,  606 => 204,  604 => 203,  602 => 449,  597 => 199,  591 => 198,  588 => 197,  583 => 194,  579 => 193,  575 => 192,  570 => 191,  566 => 189,  562 => 231,  558 => 187,  554 => 186,  550 => 185,  547 => 411,  529 => 182,  527 => 409,  515 => 404,  509 => 175,  506 => 205,  501 => 173,  498 => 203,  488 => 167,  485 => 166,  467 => 163,  463 => 162,  455 => 159,  449 => 198,  446 => 197,  441 => 196,  438 => 154,  429 => 188,  425 => 149,  421 => 148,  417 => 147,  406 => 144,  392 => 142,  386 => 159,  378 => 157,  372 => 135,  369 => 201,  364 => 133,  361 => 152,  356 => 328,  352 => 128,  348 => 140,  345 => 147,  333 => 124,  331 => 140,  327 => 114,  323 => 128,  317 => 119,  306 => 107,  301 => 113,  297 => 276,  267 => 101,  259 => 103,  242 => 113,  237 => 92,  221 => 88,  213 => 78,  200 => 72,  190 => 76,  118 => 49,  153 => 77,  102 => 30,  100 => 36,  113 => 40,  110 => 38,  97 => 41,  1357 => 388,  1348 => 387,  1346 => 386,  1343 => 385,  1327 => 381,  1320 => 380,  1318 => 379,  1315 => 378,  1292 => 374,  1267 => 373,  1265 => 372,  1262 => 371,  1250 => 366,  1245 => 365,  1243 => 364,  1240 => 363,  1231 => 357,  1225 => 355,  1222 => 354,  1217 => 353,  1215 => 352,  1212 => 351,  1205 => 346,  1196 => 344,  1192 => 343,  1189 => 342,  1186 => 341,  1184 => 340,  1181 => 339,  1173 => 335,  1171 => 334,  1168 => 333,  1162 => 329,  1156 => 327,  1153 => 326,  1151 => 325,  1148 => 324,  1139 => 319,  1137 => 318,  1114 => 317,  1111 => 316,  1108 => 315,  1105 => 314,  1102 => 313,  1099 => 312,  1096 => 311,  1094 => 310,  1091 => 309,  1084 => 305,  1080 => 304,  1075 => 303,  1073 => 302,  1070 => 301,  1063 => 296,  1060 => 295,  1052 => 290,  1049 => 289,  1047 => 288,  1044 => 287,  1036 => 282,  1032 => 281,  1028 => 280,  1025 => 279,  1023 => 278,  1020 => 277,  1012 => 273,  1010 => 269,  1008 => 268,  1005 => 267,  1000 => 263,  978 => 258,  975 => 257,  972 => 256,  969 => 255,  966 => 254,  963 => 253,  960 => 252,  957 => 251,  954 => 250,  951 => 249,  948 => 248,  946 => 247,  943 => 246,  935 => 240,  932 => 239,  930 => 238,  927 => 237,  919 => 233,  916 => 232,  914 => 231,  911 => 230,  899 => 226,  896 => 225,  893 => 224,  890 => 223,  888 => 222,  885 => 221,  877 => 217,  874 => 216,  872 => 215,  869 => 214,  861 => 210,  858 => 209,  856 => 208,  853 => 20,  845 => 203,  842 => 15,  840 => 201,  837 => 13,  829 => 196,  826 => 195,  824 => 194,  821 => 193,  813 => 189,  810 => 188,  808 => 317,  805 => 186,  797 => 182,  794 => 181,  792 => 485,  789 => 179,  781 => 175,  779 => 174,  776 => 297,  768 => 169,  765 => 168,  763 => 167,  760 => 166,  752 => 162,  749 => 161,  747 => 160,  745 => 476,  742 => 475,  735 => 153,  725 => 152,  720 => 151,  717 => 261,  711 => 148,  708 => 255,  706 => 472,  703 => 145,  695 => 139,  693 => 317,  692 => 137,  691 => 136,  690 => 467,  685 => 134,  679 => 132,  676 => 131,  674 => 303,  671 => 129,  662 => 228,  658 => 122,  654 => 121,  650 => 120,  645 => 460,  639 => 117,  636 => 116,  634 => 278,  631 => 114,  615 => 110,  613 => 265,  610 => 108,  594 => 104,  592 => 253,  589 => 102,  572 => 98,  560 => 96,  553 => 93,  551 => 223,  546 => 91,  543 => 219,  525 => 408,  523 => 212,  520 => 406,  511 => 82,  508 => 206,  505 => 80,  499 => 78,  497 => 77,  492 => 199,  489 => 75,  486 => 74,  471 => 72,  459 => 69,  456 => 68,  450 => 182,  442 => 62,  433 => 151,  428 => 59,  426 => 173,  414 => 170,  408 => 176,  405 => 49,  403 => 48,  400 => 47,  390 => 141,  388 => 42,  385 => 41,  377 => 37,  371 => 156,  366 => 33,  363 => 153,  350 => 26,  344 => 318,  342 => 317,  335 => 134,  332 => 116,  316 => 16,  313 => 15,  311 => 14,  308 => 13,  299 => 8,  293 => 120,  290 => 119,  281 => 114,  271 => 119,  266 => 363,  263 => 95,  260 => 360,  255 => 101,  253 => 100,  250 => 97,  248 => 97,  245 => 96,  240 => 108,  238 => 124,  233 => 87,  230 => 106,  227 => 86,  217 => 111,  215 => 277,  212 => 78,  210 => 77,  207 => 75,  204 => 264,  202 => 77,  197 => 69,  194 => 68,  191 => 67,  186 => 91,  184 => 63,  181 => 65,  179 => 89,  174 => 65,  161 => 63,  146 => 62,  104 => 31,  34 => 4,  152 => 46,  129 => 71,  124 => 44,  65 => 17,  20 => 1,  90 => 27,  76 => 27,  291 => 102,  288 => 118,  279 => 43,  276 => 111,  273 => 105,  262 => 98,  257 => 27,  243 => 92,  225 => 89,  222 => 83,  218 => 72,  180 => 76,  172 => 57,  170 => 84,  159 => 62,  150 => 55,  134 => 39,  81 => 24,  63 => 21,  77 => 25,  58 => 15,  59 => 13,  53 => 11,  23 => 3,  480 => 162,  474 => 191,  469 => 164,  461 => 70,  457 => 153,  453 => 199,  444 => 149,  440 => 148,  437 => 61,  435 => 176,  430 => 144,  427 => 143,  423 => 57,  413 => 146,  409 => 145,  407 => 131,  402 => 130,  398 => 129,  393 => 162,  387 => 164,  384 => 160,  381 => 120,  379 => 119,  374 => 155,  368 => 34,  365 => 200,  362 => 110,  360 => 109,  355 => 150,  341 => 118,  337 => 22,  322 => 183,  314 => 181,  312 => 130,  309 => 129,  305 => 95,  298 => 120,  294 => 90,  285 => 3,  283 => 115,  278 => 98,  268 => 130,  264 => 84,  258 => 94,  252 => 80,  247 => 115,  241 => 93,  235 => 89,  229 => 87,  224 => 81,  220 => 81,  214 => 109,  208 => 76,  169 => 207,  143 => 51,  140 => 58,  132 => 27,  128 => 62,  119 => 40,  107 => 37,  71 => 23,  177 => 89,  165 => 83,  160 => 82,  135 => 62,  126 => 70,  114 => 64,  84 => 25,  70 => 19,  67 => 16,  61 => 12,  38 => 5,  94 => 38,  89 => 30,  85 => 26,  75 => 22,  68 => 12,  56 => 12,  87 => 26,  21 => 2,  26 => 3,  93 => 28,  88 => 30,  78 => 24,  46 => 14,  27 => 4,  44 => 8,  31 => 3,  28 => 3,  201 => 94,  196 => 92,  183 => 90,  171 => 69,  166 => 54,  163 => 82,  158 => 80,  156 => 62,  151 => 59,  142 => 70,  138 => 59,  136 => 71,  121 => 50,  117 => 39,  105 => 25,  91 => 29,  62 => 14,  49 => 12,  24 => 2,  25 => 35,  19 => 1,  79 => 29,  72 => 18,  69 => 17,  47 => 9,  40 => 11,  37 => 7,  22 => 2,  246 => 96,  157 => 66,  145 => 74,  139 => 49,  131 => 45,  123 => 61,  120 => 31,  115 => 40,  111 => 47,  108 => 33,  101 => 33,  98 => 29,  96 => 35,  83 => 30,  74 => 22,  66 => 25,  55 => 12,  52 => 12,  50 => 10,  43 => 11,  41 => 7,  35 => 4,  32 => 6,  29 => 3,  209 => 98,  203 => 73,  199 => 93,  193 => 96,  189 => 66,  187 => 75,  182 => 87,  176 => 86,  173 => 85,  168 => 61,  164 => 83,  162 => 59,  154 => 60,  149 => 55,  147 => 75,  144 => 42,  141 => 73,  133 => 49,  130 => 46,  125 => 42,  122 => 41,  116 => 57,  112 => 39,  109 => 47,  106 => 51,  103 => 36,  99 => 23,  95 => 34,  92 => 31,  86 => 39,  82 => 25,  80 => 24,  73 => 23,  64 => 17,  60 => 20,  57 => 19,  54 => 19,  51 => 37,  48 => 10,  45 => 8,  42 => 7,  39 => 10,  36 => 5,  33 => 4,  30 => 3,);
    }
}
