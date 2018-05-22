<?php

/* OlegOrderformBundle:Part:new.html.twig */
class __TwigTemplate_1b79a4db644c88685594058c0331844c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("OlegOrderformBundle::Default/base.html.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'body' => array($this, 'block_body'),
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
        // line 8
        $this->displayBlock('body', $context, $blocks);
    }

    public function block_body($context, array $blocks = array())
    {
        // line 9
        echo "<h1>Part creation</h1>

    <form action=\"";
        // line 11
        echo $this->env->getExtension('routing')->getPath("part_create");
        echo "\" method=\"post\" ";
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'enctype');
;
        echo ">
        ";
        // line 12
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'widget');
        echo "
        <p>
            <button type=\"submit\">Create</button>
        </p>
    </form>

        <ul class=\"record_actions\">
    <li>
        <a href=\"";
        // line 20
        echo $this->env->getExtension('routing')->getPath("part");
        echo "\">
            Back to the list
        </a>
    </li>
</ul>
";
    }

    public function getTemplateName()
    {
        return "OlegOrderformBundle:Part:new.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  251 => 116,  232 => 106,  185 => 78,  216 => 95,  206 => 94,  157 => 66,  127 => 57,  108 => 49,  256 => 118,  236 => 110,  226 => 102,  195 => 85,  192 => 88,  155 => 66,  728 => 23,  722 => 20,  718 => 18,  707 => 17,  698 => 320,  680 => 306,  666 => 298,  643 => 284,  628 => 275,  622 => 271,  619 => 269,  607 => 262,  598 => 256,  584 => 248,  578 => 245,  559 => 228,  557 => 227,  555 => 226,  548 => 221,  536 => 218,  533 => 217,  531 => 216,  528 => 214,  516 => 211,  514 => 210,  500 => 204,  490 => 198,  484 => 197,  482 => 196,  476 => 192,  468 => 190,  466 => 189,  460 => 185,  458 => 184,  452 => 183,  443 => 178,  439 => 177,  431 => 175,  422 => 172,  418 => 171,  410 => 169,  401 => 165,  397 => 164,  389 => 161,  357 => 148,  353 => 147,  343 => 143,  339 => 142,  319 => 137,  310 => 133,  302 => 130,  284 => 124,  280 => 123,  254 => 125,  249 => 115,  244 => 114,  231 => 104,  223 => 101,  219 => 100,  205 => 89,  175 => 74,  167 => 82,  148 => 75,  137 => 72,  848 => 18,  834 => 12,  823 => 11,  817 => 323,  814 => 321,  803 => 316,  790 => 305,  784 => 302,  770 => 294,  759 => 286,  753 => 283,  744 => 277,  738 => 274,  732 => 270,  729 => 268,  723 => 264,  702 => 322,  694 => 247,  688 => 244,  672 => 230,  665 => 229,  660 => 295,  656 => 224,  649 => 287,  646 => 222,  641 => 218,  635 => 217,  632 => 216,  627 => 212,  621 => 211,  618 => 210,  612 => 205,  606 => 204,  604 => 203,  602 => 202,  597 => 199,  591 => 198,  588 => 197,  583 => 194,  579 => 193,  575 => 192,  570 => 191,  566 => 189,  562 => 231,  558 => 187,  554 => 186,  550 => 185,  547 => 184,  529 => 182,  527 => 181,  515 => 177,  509 => 175,  506 => 205,  501 => 173,  498 => 203,  488 => 167,  485 => 166,  467 => 163,  463 => 162,  455 => 159,  449 => 157,  446 => 156,  441 => 155,  438 => 154,  429 => 150,  425 => 149,  421 => 148,  417 => 147,  406 => 144,  392 => 142,  386 => 140,  378 => 156,  372 => 135,  369 => 134,  364 => 133,  361 => 150,  356 => 129,  352 => 128,  348 => 146,  345 => 126,  333 => 124,  331 => 140,  327 => 139,  323 => 138,  317 => 119,  306 => 132,  301 => 113,  297 => 112,  267 => 118,  259 => 127,  242 => 113,  237 => 92,  221 => 88,  213 => 86,  200 => 87,  190 => 83,  118 => 38,  153 => 56,  102 => 32,  100 => 44,  113 => 36,  110 => 50,  97 => 37,  1357 => 388,  1348 => 387,  1346 => 386,  1343 => 385,  1327 => 381,  1320 => 380,  1318 => 379,  1315 => 378,  1292 => 374,  1267 => 373,  1265 => 372,  1262 => 371,  1250 => 366,  1245 => 365,  1243 => 364,  1240 => 363,  1231 => 357,  1225 => 355,  1222 => 354,  1217 => 353,  1215 => 352,  1212 => 351,  1205 => 346,  1196 => 344,  1192 => 343,  1189 => 342,  1186 => 341,  1184 => 340,  1181 => 339,  1173 => 335,  1171 => 334,  1168 => 333,  1162 => 329,  1156 => 327,  1153 => 326,  1151 => 325,  1148 => 324,  1139 => 319,  1137 => 318,  1114 => 317,  1111 => 316,  1108 => 315,  1105 => 314,  1102 => 313,  1099 => 312,  1096 => 311,  1094 => 310,  1091 => 309,  1084 => 305,  1080 => 304,  1075 => 303,  1073 => 302,  1070 => 301,  1063 => 296,  1060 => 295,  1052 => 290,  1049 => 289,  1047 => 288,  1044 => 287,  1036 => 282,  1032 => 281,  1028 => 280,  1025 => 279,  1023 => 278,  1020 => 277,  1012 => 273,  1010 => 269,  1008 => 268,  1005 => 267,  1000 => 263,  978 => 258,  975 => 257,  972 => 256,  969 => 255,  966 => 254,  963 => 253,  960 => 252,  957 => 251,  954 => 250,  951 => 249,  948 => 248,  946 => 247,  943 => 246,  935 => 240,  932 => 239,  930 => 238,  927 => 237,  919 => 233,  916 => 232,  914 => 231,  911 => 230,  899 => 226,  896 => 225,  893 => 224,  890 => 223,  888 => 222,  885 => 221,  877 => 217,  874 => 216,  872 => 215,  869 => 214,  861 => 210,  858 => 209,  856 => 208,  853 => 20,  845 => 203,  842 => 15,  840 => 201,  837 => 13,  829 => 196,  826 => 195,  824 => 194,  821 => 193,  813 => 189,  810 => 188,  808 => 317,  805 => 186,  797 => 182,  794 => 181,  792 => 180,  789 => 179,  781 => 175,  779 => 174,  776 => 297,  768 => 169,  765 => 168,  763 => 167,  760 => 166,  752 => 162,  749 => 161,  747 => 160,  745 => 159,  742 => 158,  735 => 153,  725 => 152,  720 => 151,  717 => 261,  711 => 148,  708 => 255,  706 => 146,  703 => 145,  695 => 139,  693 => 317,  692 => 137,  691 => 136,  690 => 135,  685 => 134,  679 => 132,  676 => 131,  674 => 303,  671 => 129,  662 => 228,  658 => 122,  654 => 121,  650 => 120,  645 => 119,  639 => 117,  636 => 116,  634 => 278,  631 => 114,  615 => 110,  613 => 265,  610 => 108,  594 => 104,  592 => 253,  589 => 102,  572 => 98,  560 => 96,  553 => 93,  551 => 223,  546 => 91,  543 => 219,  525 => 89,  523 => 212,  520 => 87,  511 => 82,  508 => 206,  505 => 80,  499 => 78,  497 => 77,  492 => 199,  489 => 75,  486 => 74,  471 => 72,  459 => 69,  456 => 68,  450 => 182,  442 => 62,  433 => 151,  428 => 59,  426 => 173,  414 => 170,  408 => 50,  405 => 49,  403 => 48,  400 => 47,  390 => 141,  388 => 42,  385 => 41,  377 => 37,  371 => 35,  366 => 33,  363 => 32,  350 => 26,  344 => 24,  342 => 23,  335 => 141,  332 => 20,  316 => 16,  313 => 15,  311 => 14,  308 => 13,  299 => 8,  293 => 128,  290 => 5,  281 => 385,  271 => 119,  266 => 363,  263 => 128,  260 => 360,  255 => 350,  253 => 116,  250 => 97,  248 => 122,  245 => 96,  240 => 108,  238 => 110,  233 => 91,  230 => 106,  227 => 103,  217 => 101,  215 => 277,  212 => 99,  210 => 91,  207 => 89,  204 => 264,  202 => 87,  197 => 86,  194 => 245,  191 => 82,  186 => 236,  184 => 85,  181 => 229,  179 => 78,  174 => 73,  161 => 199,  146 => 62,  104 => 33,  74 => 26,  34 => 6,  83 => 33,  152 => 64,  145 => 54,  131 => 45,  129 => 44,  124 => 39,  65 => 19,  120 => 38,  20 => 2,  90 => 37,  76 => 29,  291 => 61,  288 => 125,  279 => 43,  276 => 378,  273 => 105,  262 => 28,  257 => 27,  246 => 114,  243 => 324,  225 => 89,  222 => 107,  218 => 72,  180 => 76,  172 => 73,  170 => 72,  159 => 67,  150 => 58,  134 => 51,  81 => 34,  63 => 17,  96 => 30,  77 => 32,  58 => 19,  52 => 11,  59 => 16,  53 => 21,  23 => 3,  480 => 162,  474 => 191,  469 => 164,  461 => 70,  457 => 153,  453 => 151,  444 => 149,  440 => 148,  437 => 61,  435 => 176,  430 => 144,  427 => 143,  423 => 57,  413 => 146,  409 => 145,  407 => 131,  402 => 130,  398 => 129,  393 => 162,  387 => 122,  384 => 160,  381 => 120,  379 => 119,  374 => 155,  368 => 34,  365 => 151,  362 => 110,  360 => 109,  355 => 27,  341 => 105,  337 => 22,  322 => 101,  314 => 118,  312 => 98,  309 => 117,  305 => 95,  298 => 129,  294 => 90,  285 => 3,  283 => 107,  278 => 384,  268 => 130,  264 => 84,  258 => 118,  252 => 80,  247 => 115,  241 => 77,  235 => 308,  229 => 90,  224 => 102,  220 => 103,  214 => 95,  208 => 91,  169 => 207,  143 => 54,  140 => 55,  132 => 69,  128 => 67,  119 => 52,  111 => 35,  107 => 41,  71 => 21,  177 => 74,  165 => 70,  160 => 61,  139 => 73,  135 => 39,  126 => 66,  114 => 46,  84 => 25,  70 => 25,  67 => 23,  61 => 2,  47 => 15,  38 => 9,  94 => 30,  89 => 32,  85 => 36,  79 => 32,  75 => 25,  68 => 35,  56 => 22,  50 => 12,  29 => 7,  87 => 27,  72 => 36,  55 => 17,  21 => 4,  26 => 5,  98 => 31,  93 => 35,  88 => 30,  78 => 27,  46 => 12,  27 => 4,  40 => 7,  44 => 12,  35 => 6,  31 => 4,  43 => 8,  41 => 25,  28 => 3,  201 => 94,  196 => 87,  183 => 78,  171 => 74,  166 => 206,  163 => 68,  158 => 80,  156 => 192,  151 => 56,  142 => 52,  138 => 60,  136 => 47,  123 => 56,  121 => 56,  117 => 25,  115 => 36,  105 => 36,  101 => 35,  91 => 28,  69 => 25,  66 => 24,  62 => 23,  49 => 12,  24 => 5,  32 => 8,  25 => 29,  22 => 4,  19 => 2,  209 => 98,  203 => 78,  199 => 86,  193 => 92,  189 => 82,  187 => 81,  182 => 76,  176 => 220,  173 => 74,  168 => 70,  164 => 200,  162 => 68,  154 => 65,  149 => 55,  147 => 55,  144 => 74,  141 => 62,  133 => 45,  130 => 50,  125 => 48,  122 => 39,  116 => 54,  112 => 39,  109 => 35,  106 => 33,  103 => 36,  99 => 68,  95 => 35,  92 => 29,  86 => 25,  82 => 28,  80 => 24,  73 => 21,  64 => 18,  60 => 20,  57 => 16,  54 => 16,  51 => 14,  48 => 13,  45 => 6,  42 => 11,  39 => 10,  36 => 8,  33 => 7,  30 => 3,);
    }
}
