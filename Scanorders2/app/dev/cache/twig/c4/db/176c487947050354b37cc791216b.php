<?php

/* WebProfilerBundle:Collector:exception.html.twig */
class __TwigTemplate_c4db176c487947050354b37cc791216b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@WebProfiler/Profiler/layout.html.twig");

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'menu' => array($this, 'block_menu'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasexception")) {
            // line 5
            echo "        <style>
            ";
            // line 6
            echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('routing')->getPath("_profiler_exception_css", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")))));
            echo "
        </style>
    ";
        }
        // line 9
        echo "    ";
        $this->displayParentBlock("head", $context, $blocks);
        echo "
";
    }

    // line 12
    public function block_menu($context, array $blocks = array())
    {
        // line 13
        echo "<span class=\"label\">
    <span class=\"icon\"><img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAeCAQAAADwIURrAAAEQ0lEQVR42sWVf0xbVRTHKSCUUNEsWdhE3BT3QzNMjHEydLz+eONnGS2sbBSkKBA7Nn6DGwb+EByLbMbFKEuUiG1kTrFgwmCODZaZqaGJEQYSMXQJMJFBs/pGlV9tv97bAukrZMD+8Z2k957vOfdzz7s579brwU+jSP2mojnmNgOXxQ4pLqa90SjyetjHEKQ6I7MwWGkyi+qMIWjDQPgUiuNMfBTf4kxlkfDZELJSynIMHmwsVyldNxaCC7soUjV/fcTawxmvjCscS6AUR9cdzsgZu0YVCwyiLV/uhGB9UFFmG4O0OXM7inEQCpTf6ZY7nEjbeCdKkUSs9O73iTYGmW0QrQfprWclBNHSAxWegD+ECEXmp0MU2nQLajxJFCH5VTfdYiBx6Fl4r6POYj0FcCcQAoFrG4T1fkS14VpscyEgwLaRU1Qr1rtqhY9mb7L6stCtuooIyd8JnSUvEkCoepiclg1y+C3HHx3NpoBZFQKWtQAoqaYeRijxfAvSxXYGWbXwX074oIwmFJ5FIMLlVjrvT4K/JlfKSTlNLkTf5VOtKwtCrUJ2VzKdXoaAH9VUk1sRTgiPlzdSr/IrbCbAvMi4SyWpprfoOd4sxyZEsDYarqrHM6wTz1qxu6CNzkq/xtMJY3QmWTDuLbtAZ1I7XkuR6V5pbCAqjNUIJlB1BwOx/T1DDpf678DxI5XDysUP8r4xO3bOOZu7USRbcOLnftCm3HOhrlWwJEoNh6TWmMn6VrLplDE/5/XsHV7aXxchNlorgys1Sz0Zb62YoGP5ZMzskhY9WzlKRy0XN7OkIdfwWeI/DJYs6abXUO3pybyZOnOCPd72xcAlPU4y2JjhMNKgky8ccMicZR360wv78Q4+4Vroidz+HEpkbhjKYmt3FUMG43iVtW5q7EPSLtiO8MES5wtbtKfa8/lLNHZPSIY9nue3Hs+oieHozHoeNTgJiaulfMFmTK9WRdW0+arEwde6rp+dWi035x4UCMNTEC02P14wn3/3PrsisWgUKrXOXVF2QH5sxDPvgOO0xXIOz/OuFzwGCWptHX2/XZtwT5ZbJ15i/Jj6ZaW+UNgRw9rcc7r/6htAG6oRhSCP6w4i7IAYh1HHryGz07AZAmYXk0VsCwSdW5N/52fgfaQSYBgCV70G4UvQiZ6vFjuWXq1JyguBT+GzGeWx455xJCJwjcsa4g23lJiu+/+h0R6I+IeCRiXM87rPzm+0fFssz0+YR9Ta0H3ZZl77W4/yNrk4XjDj7mebsW9taHjDDfdFP/W0DLp9ojOc7vLP7vGmq9izNnQLtB+PLZ6fo3kAxTihM7mO4Ijtj2YooW0edJ20BDoTchC8NtQPe/D2XHtvv+kXfIOjeI74RWgZ7OvtXWhAEoKxE8fwLfH70Uoiu/HIev6khdgOMZJxEBEIgR/8EYpXoYQCL2MTvOFH1EjiJ2M/ifivJPwHIs9MRJmsgVwAAAAASUVORK5CYII=\" alt=\"Exception\"></span>
    <strong>Exception</strong>
    <span class=\"count\">
        ";
        // line 17
        if ($this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasexception")) {
            // line 18
            echo "            <span>1</span>
        ";
        }
        // line 20
        echo "    </span>
</span>
";
    }

    // line 24
    public function block_panel($context, array $blocks = array())
    {
        // line 25
        echo "    <h2>Exception</h2>

    ";
        // line 27
        if ((!$this->getAttribute((isset($context["collector"]) ? $context["collector"] : $this->getContext($context, "collector")), "hasexception"))) {
            // line 28
            echo "        <p>
            <em>No exception was thrown and uncaught during the request.</em>
        </p>
    ";
        } else {
            // line 32
            echo "        <div class=\"sf-reset\">
            ";
            // line 33
            echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('routing')->getPath("_profiler_exception", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")))));
            echo "
        </div>
    ";
        }
    }

    public function getTemplateName()
    {
        return "WebProfilerBundle:Collector:exception.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  462 => 202,  415 => 180,  394 => 168,  380 => 160,  373 => 156,  351 => 141,  338 => 135,  329 => 131,  325 => 129,  320 => 127,  315 => 125,  303 => 122,  300 => 121,  289 => 113,  286 => 112,  270 => 102,  178 => 66,  376 => 206,  349 => 196,  330 => 187,  326 => 184,  318 => 182,  307 => 176,  275 => 105,  269 => 143,  261 => 138,  251 => 116,  232 => 106,  185 => 66,  216 => 79,  206 => 94,  127 => 60,  256 => 96,  236 => 110,  226 => 84,  195 => 93,  192 => 88,  155 => 66,  728 => 23,  722 => 20,  718 => 18,  707 => 17,  698 => 320,  680 => 306,  666 => 298,  643 => 284,  628 => 275,  622 => 271,  619 => 269,  607 => 262,  598 => 256,  584 => 248,  578 => 245,  559 => 228,  557 => 227,  555 => 226,  548 => 221,  536 => 218,  533 => 217,  531 => 216,  528 => 214,  516 => 211,  514 => 210,  500 => 204,  490 => 198,  484 => 197,  482 => 196,  476 => 192,  468 => 190,  466 => 189,  460 => 185,  458 => 184,  452 => 183,  443 => 178,  439 => 195,  431 => 189,  422 => 184,  418 => 171,  410 => 169,  401 => 172,  397 => 164,  389 => 161,  357 => 198,  353 => 197,  343 => 143,  339 => 142,  319 => 137,  310 => 133,  302 => 130,  284 => 124,  280 => 123,  254 => 125,  249 => 115,  244 => 127,  231 => 104,  223 => 115,  219 => 100,  205 => 97,  175 => 65,  167 => 84,  148 => 73,  137 => 33,  848 => 18,  834 => 12,  823 => 11,  817 => 323,  814 => 321,  803 => 316,  790 => 305,  784 => 302,  770 => 294,  759 => 286,  753 => 283,  744 => 277,  738 => 274,  732 => 270,  729 => 268,  723 => 264,  702 => 322,  694 => 247,  688 => 244,  672 => 230,  665 => 229,  660 => 295,  656 => 224,  649 => 287,  646 => 222,  641 => 218,  635 => 217,  632 => 216,  627 => 212,  621 => 211,  618 => 210,  612 => 205,  606 => 204,  604 => 203,  602 => 202,  597 => 199,  591 => 198,  588 => 197,  583 => 194,  579 => 193,  575 => 192,  570 => 191,  566 => 189,  562 => 231,  558 => 187,  554 => 186,  550 => 185,  547 => 184,  529 => 182,  527 => 181,  515 => 177,  509 => 175,  506 => 205,  501 => 173,  498 => 203,  488 => 167,  485 => 166,  467 => 163,  463 => 162,  455 => 159,  449 => 198,  446 => 197,  441 => 196,  438 => 154,  429 => 188,  425 => 149,  421 => 148,  417 => 147,  406 => 144,  392 => 142,  386 => 140,  378 => 156,  372 => 135,  369 => 201,  364 => 133,  361 => 146,  356 => 129,  352 => 128,  348 => 140,  345 => 126,  333 => 124,  331 => 140,  327 => 139,  323 => 128,  317 => 119,  306 => 132,  301 => 113,  297 => 112,  267 => 101,  259 => 127,  242 => 113,  237 => 92,  221 => 88,  213 => 78,  200 => 72,  190 => 83,  118 => 49,  153 => 56,  102 => 40,  100 => 39,  113 => 48,  110 => 22,  97 => 41,  1357 => 388,  1348 => 387,  1346 => 386,  1343 => 385,  1327 => 381,  1320 => 380,  1318 => 379,  1315 => 378,  1292 => 374,  1267 => 373,  1265 => 372,  1262 => 371,  1250 => 366,  1245 => 365,  1243 => 364,  1240 => 363,  1231 => 357,  1225 => 355,  1222 => 354,  1217 => 353,  1215 => 352,  1212 => 351,  1205 => 346,  1196 => 344,  1192 => 343,  1189 => 342,  1186 => 341,  1184 => 340,  1181 => 339,  1173 => 335,  1171 => 334,  1168 => 333,  1162 => 329,  1156 => 327,  1153 => 326,  1151 => 325,  1148 => 324,  1139 => 319,  1137 => 318,  1114 => 317,  1111 => 316,  1108 => 315,  1105 => 314,  1102 => 313,  1099 => 312,  1096 => 311,  1094 => 310,  1091 => 309,  1084 => 305,  1080 => 304,  1075 => 303,  1073 => 302,  1070 => 301,  1063 => 296,  1060 => 295,  1052 => 290,  1049 => 289,  1047 => 288,  1044 => 287,  1036 => 282,  1032 => 281,  1028 => 280,  1025 => 279,  1023 => 278,  1020 => 277,  1012 => 273,  1010 => 269,  1008 => 268,  1005 => 267,  1000 => 263,  978 => 258,  975 => 257,  972 => 256,  969 => 255,  966 => 254,  963 => 253,  960 => 252,  957 => 251,  954 => 250,  951 => 249,  948 => 248,  946 => 247,  943 => 246,  935 => 240,  932 => 239,  930 => 238,  927 => 237,  919 => 233,  916 => 232,  914 => 231,  911 => 230,  899 => 226,  896 => 225,  893 => 224,  890 => 223,  888 => 222,  885 => 221,  877 => 217,  874 => 216,  872 => 215,  869 => 214,  861 => 210,  858 => 209,  856 => 208,  853 => 20,  845 => 203,  842 => 15,  840 => 201,  837 => 13,  829 => 196,  826 => 195,  824 => 194,  821 => 193,  813 => 189,  810 => 188,  808 => 317,  805 => 186,  797 => 182,  794 => 181,  792 => 180,  789 => 179,  781 => 175,  779 => 174,  776 => 297,  768 => 169,  765 => 168,  763 => 167,  760 => 166,  752 => 162,  749 => 161,  747 => 160,  745 => 159,  742 => 158,  735 => 153,  725 => 152,  720 => 151,  717 => 261,  711 => 148,  708 => 255,  706 => 146,  703 => 145,  695 => 139,  693 => 317,  692 => 137,  691 => 136,  690 => 135,  685 => 134,  679 => 132,  676 => 131,  674 => 303,  671 => 129,  662 => 228,  658 => 122,  654 => 121,  650 => 120,  645 => 119,  639 => 117,  636 => 116,  634 => 278,  631 => 114,  615 => 110,  613 => 265,  610 => 108,  594 => 104,  592 => 253,  589 => 102,  572 => 98,  560 => 96,  553 => 93,  551 => 223,  546 => 91,  543 => 219,  525 => 89,  523 => 212,  520 => 87,  511 => 82,  508 => 206,  505 => 80,  499 => 78,  497 => 77,  492 => 199,  489 => 75,  486 => 74,  471 => 72,  459 => 69,  456 => 68,  450 => 182,  442 => 62,  433 => 151,  428 => 59,  426 => 173,  414 => 170,  408 => 176,  405 => 49,  403 => 48,  400 => 47,  390 => 141,  388 => 42,  385 => 41,  377 => 37,  371 => 35,  366 => 33,  363 => 32,  350 => 26,  344 => 195,  342 => 137,  335 => 134,  332 => 20,  316 => 16,  313 => 15,  311 => 14,  308 => 13,  299 => 8,  293 => 128,  290 => 5,  281 => 385,  271 => 119,  266 => 363,  263 => 128,  260 => 360,  255 => 135,  253 => 116,  250 => 97,  248 => 94,  245 => 96,  240 => 108,  238 => 124,  233 => 87,  230 => 106,  227 => 103,  217 => 111,  215 => 277,  212 => 99,  210 => 100,  207 => 75,  204 => 264,  202 => 102,  197 => 71,  194 => 70,  191 => 67,  186 => 91,  184 => 85,  181 => 65,  179 => 89,  174 => 73,  161 => 63,  146 => 62,  104 => 59,  34 => 4,  152 => 64,  129 => 71,  124 => 44,  65 => 23,  20 => 2,  90 => 27,  76 => 25,  291 => 61,  288 => 157,  279 => 43,  276 => 378,  273 => 105,  262 => 98,  257 => 27,  243 => 324,  225 => 89,  222 => 107,  218 => 72,  180 => 76,  172 => 64,  170 => 88,  159 => 62,  150 => 55,  134 => 54,  81 => 23,  63 => 18,  77 => 33,  58 => 17,  59 => 14,  53 => 12,  23 => 3,  480 => 162,  474 => 191,  469 => 164,  461 => 70,  457 => 153,  453 => 199,  444 => 149,  440 => 148,  437 => 61,  435 => 176,  430 => 144,  427 => 143,  423 => 57,  413 => 146,  409 => 145,  407 => 131,  402 => 130,  398 => 129,  393 => 162,  387 => 164,  384 => 160,  381 => 120,  379 => 119,  374 => 155,  368 => 34,  365 => 200,  362 => 110,  360 => 109,  355 => 143,  341 => 105,  337 => 22,  322 => 183,  314 => 181,  312 => 124,  309 => 117,  305 => 95,  298 => 120,  294 => 90,  285 => 3,  283 => 107,  278 => 106,  268 => 130,  264 => 84,  258 => 118,  252 => 80,  247 => 115,  241 => 90,  235 => 308,  229 => 85,  224 => 102,  220 => 81,  214 => 109,  208 => 105,  169 => 207,  143 => 54,  140 => 58,  132 => 27,  128 => 62,  119 => 40,  107 => 49,  71 => 16,  177 => 89,  165 => 60,  160 => 82,  135 => 62,  126 => 70,  114 => 64,  84 => 24,  70 => 19,  67 => 20,  61 => 17,  38 => 6,  94 => 34,  89 => 33,  85 => 32,  75 => 27,  68 => 35,  56 => 11,  87 => 34,  21 => 1,  26 => 9,  93 => 43,  88 => 32,  78 => 26,  46 => 13,  27 => 3,  44 => 7,  31 => 3,  28 => 3,  201 => 94,  196 => 87,  183 => 90,  171 => 69,  166 => 206,  163 => 68,  158 => 62,  156 => 58,  151 => 59,  142 => 70,  138 => 59,  136 => 73,  121 => 50,  117 => 19,  105 => 34,  91 => 33,  62 => 21,  49 => 14,  24 => 1,  25 => 35,  19 => 1,  79 => 27,  72 => 28,  69 => 24,  47 => 8,  40 => 6,  37 => 5,  22 => 4,  246 => 93,  157 => 66,  145 => 81,  139 => 63,  131 => 61,  123 => 42,  120 => 20,  115 => 36,  111 => 47,  108 => 19,  101 => 43,  98 => 45,  96 => 37,  83 => 33,  74 => 27,  66 => 25,  55 => 13,  52 => 12,  50 => 18,  43 => 12,  41 => 10,  35 => 6,  32 => 5,  29 => 3,  209 => 98,  203 => 73,  199 => 105,  193 => 96,  189 => 92,  187 => 93,  182 => 76,  176 => 63,  173 => 85,  168 => 61,  164 => 83,  162 => 59,  154 => 60,  149 => 55,  147 => 54,  144 => 74,  141 => 51,  133 => 49,  130 => 46,  125 => 51,  122 => 68,  116 => 39,  112 => 52,  109 => 44,  106 => 45,  103 => 36,  99 => 31,  95 => 42,  92 => 35,  86 => 39,  82 => 28,  80 => 27,  73 => 24,  64 => 23,  60 => 22,  57 => 12,  54 => 16,  51 => 17,  48 => 9,  45 => 9,  42 => 7,  39 => 6,  36 => 5,  33 => 4,  30 => 3,);
    }
}
