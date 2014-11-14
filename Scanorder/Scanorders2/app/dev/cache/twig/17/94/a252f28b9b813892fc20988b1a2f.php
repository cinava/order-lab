<?php

/* OlegOrderformBundle:Order:new.html.twig */
class __TwigTemplate_1794a252f28b9b813892fc20988b1a2f extends Twig_Template
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
        echo " 
";
        // line 8
        $this->displayBlock('body', $context, $blocks);
    }

    public function block_body($context, array $blocks = array())
    {
        // line 10
        echo "<h1>Multi-Scan Order Form</h1>

";
        // line 14
        echo "    
    
    
    
    
    
    
    
    <form action=\"";
        // line 22
        echo $this->env->getExtension('routing')->getPath("orderinfo_create");
        echo "\" method=\"post\" ";
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'enctype');
;
        echo ">
                     
        
        
        <div class=\"slide_new\"> 
            ";
        // line 27
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'errors');
        echo "
            ";
        // line 28
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), 'errors');
        echo "                      
            
            ";
        // line 30
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "type"), 'widget', array("value" => "single"));
        echo "
            
            ";
        // line 32
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "accession"), 'label', array("label" => "* Accession + Part + Block (i.e. S12-99998 B1)"));
        echo " 
            ";
        // line 33
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "accession"), 'widget');
        echo "   
            
            ";
        // line 35
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "part"), "name"), 'label');
        echo "
            ";
        // line 36
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "part"), "name"), 'widget');
        echo "        
            ";
        // line 37
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "block"), "name"), 'widget');
        echo "          
            
";
        // line 40
        echo "            ";
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "stain"), "name"), 'label');
        echo "
";
        // line 42
        echo "            ";
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "stain"), "name"), 'widget');
        echo "
                      
            ";
        // line 44
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "mag"), 'label');
        echo "
            ";
        // line 45
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "mag"), 'widget');
        echo " 
            
            ";
        // line 47
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "part"), "diagnosis"), 'label');
        echo "
            ";
        // line 48
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "part"), "diagnosis"), 'widget');
        echo " 
            
            ";
        // line 50
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "note"), 'label');
        echo "
            ";
        // line 51
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "note"), 'widget');
        echo " 
            
            <div class=\"btn_margin_top\">
                <input type='button' id='next' value='Next' class=\"btn btn-primary btn-success\">       
            </div>
            
            <a class='btn_margin_top btn btn-primary' href=\"";
        // line 57
        echo $this->env->getExtension('routing')->getPath("orderinfo");
        echo "\">Back to the list</a>
            
        </div>

        <div class=\"slide_new\"> 
            
            <div id=\"orderinfo\">
                       
            <h4>Scan Order Info</h4>           
            ";
        // line 66
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'errors');
        echo "           
            ";
        // line 67
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'label');
        echo "
            ";
        // line 68
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'widget');
        echo "
                   
            <p>
                <button class=\"btn_margin_top btn btn-primary btn-success\" type=\"submit\">Submit</button>        
            </p>
            
        </div> 
            
        </div>    
        
        <div class=\"slide_new\">                     
            
            <a id=\"optional_button\" class=\"btn btn-mini\" href=\"#\">options</a>
        
            <div id=\"optional\">
            
                ";
        // line 84
        if ((!twig_test_empty($this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "accession"), 'rest')))) {
            // line 85
            echo "                    <h4>Optional Parameters for Accession</h4>
                    ";
            // line 86
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "accession"), 'rest');
            echo " 
                ";
        }
        // line 88
        echo "                 
";
        // line 90
        echo "                <button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#slide_part\">
                    Optional Parameters for Part
                </button>
                <div id=\"slide_part\" class=\"collapse\">
                    ";
        // line 94
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "part"), 'rest');
        echo "
                </div>

                ";
        // line 97
        if ((!twig_test_empty($this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "block"), 'rest')))) {
            // line 98
            echo "                    <h4>Optional Parameters for Block</h4>
                    ";
            // line 99
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "block"), 'rest');
            echo " 
                ";
        }
        // line 101
        echo "
";
        // line 103
        echo "                <button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#slide_stain\">
                    Optional Parameters for Stain
                </button>
                <div id=\"slide_stain\" class=\"collapse\">
                    ";
        // line 107
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), "stain"), 'rest');
        echo " 
                </div>

                <button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#slide_slide\">
                    Optional Parameters for Slide
                </button>
                <div id=\"slide_slide\" class=\"collapse\">
                    ";
        // line 114
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), "slide"), 'rest');
        echo " 
                </div>
                    
";
        // line 118
        echo "                <button type=\"button\" class=\"btn btn-info\" data-toggle=\"collapse\" data-target=\"#slide_scan\">
                    Optional Parameters for Scan
                </button>
                <div id=\"slide_scan\" class=\"collapse\">
                    ";
        // line 122
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), 'rest');
        echo " 
                </div>                               
                    
                ";
        // line 125
        if (((!twig_test_empty($this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), 'rest'))) && (!twig_test_empty($this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'rest'))))) {
            // line 126
            echo "                    <h4>All Other Parameters</h4> 
                    ";
            // line 127
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'rest');
            echo " 
                    ";
            // line 128
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form_scan"]) ? $context["form_scan"] : $this->getContext($context, "form_scan")), 'rest');
            echo "
                ";
        }
        // line 130
        echo "            </div> 
            
        </div>

";
    }

    public function getTemplateName()
    {
        return "OlegOrderformBundle:Order:new.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  256 => 126,  236 => 114,  226 => 107,  195 => 90,  192 => 88,  155 => 66,  728 => 23,  722 => 20,  718 => 18,  707 => 17,  698 => 320,  680 => 306,  666 => 298,  643 => 284,  628 => 275,  622 => 271,  619 => 269,  607 => 262,  598 => 256,  584 => 248,  578 => 245,  559 => 228,  557 => 227,  555 => 226,  548 => 221,  536 => 218,  533 => 217,  531 => 216,  528 => 214,  516 => 211,  514 => 210,  500 => 204,  490 => 198,  484 => 197,  482 => 196,  476 => 192,  468 => 190,  466 => 189,  460 => 185,  458 => 184,  452 => 183,  443 => 178,  439 => 177,  431 => 175,  422 => 172,  418 => 171,  410 => 169,  401 => 165,  397 => 164,  389 => 161,  357 => 148,  353 => 147,  343 => 143,  339 => 142,  319 => 137,  310 => 133,  302 => 130,  284 => 124,  280 => 123,  254 => 125,  249 => 111,  244 => 110,  231 => 104,  223 => 101,  219 => 100,  205 => 95,  175 => 85,  167 => 82,  148 => 75,  137 => 72,  848 => 18,  834 => 12,  823 => 11,  817 => 323,  814 => 321,  803 => 316,  790 => 305,  784 => 302,  770 => 294,  759 => 286,  753 => 283,  744 => 277,  738 => 274,  732 => 270,  729 => 268,  723 => 264,  702 => 322,  694 => 247,  688 => 244,  672 => 230,  665 => 229,  660 => 295,  656 => 224,  649 => 287,  646 => 222,  641 => 218,  635 => 217,  632 => 216,  627 => 212,  621 => 211,  618 => 210,  612 => 205,  606 => 204,  604 => 203,  602 => 202,  597 => 199,  591 => 198,  588 => 197,  583 => 194,  579 => 193,  575 => 192,  570 => 191,  566 => 189,  562 => 231,  558 => 187,  554 => 186,  550 => 185,  547 => 184,  529 => 182,  527 => 181,  515 => 177,  509 => 175,  506 => 205,  501 => 173,  498 => 203,  488 => 167,  485 => 166,  467 => 163,  463 => 162,  455 => 159,  449 => 157,  446 => 156,  441 => 155,  438 => 154,  429 => 150,  425 => 149,  421 => 148,  417 => 147,  406 => 144,  392 => 142,  386 => 140,  378 => 156,  372 => 135,  369 => 134,  364 => 133,  361 => 150,  356 => 129,  352 => 128,  348 => 146,  345 => 126,  333 => 124,  331 => 140,  327 => 139,  323 => 138,  317 => 119,  306 => 132,  301 => 113,  297 => 112,  267 => 118,  259 => 127,  242 => 118,  237 => 92,  221 => 88,  213 => 86,  200 => 82,  190 => 78,  118 => 52,  153 => 62,  102 => 34,  100 => 47,  113 => 57,  110 => 50,  97 => 37,  1357 => 388,  1348 => 387,  1346 => 386,  1343 => 385,  1327 => 381,  1320 => 380,  1318 => 379,  1315 => 378,  1292 => 374,  1267 => 373,  1265 => 372,  1262 => 371,  1250 => 366,  1245 => 365,  1243 => 364,  1240 => 363,  1231 => 357,  1225 => 355,  1222 => 354,  1217 => 353,  1215 => 352,  1212 => 351,  1205 => 346,  1196 => 344,  1192 => 343,  1189 => 342,  1186 => 341,  1184 => 340,  1181 => 339,  1173 => 335,  1171 => 334,  1168 => 333,  1162 => 329,  1156 => 327,  1153 => 326,  1151 => 325,  1148 => 324,  1139 => 319,  1137 => 318,  1114 => 317,  1111 => 316,  1108 => 315,  1105 => 314,  1102 => 313,  1099 => 312,  1096 => 311,  1094 => 310,  1091 => 309,  1084 => 305,  1080 => 304,  1075 => 303,  1073 => 302,  1070 => 301,  1063 => 296,  1060 => 295,  1052 => 290,  1049 => 289,  1047 => 288,  1044 => 287,  1036 => 282,  1032 => 281,  1028 => 280,  1025 => 279,  1023 => 278,  1020 => 277,  1012 => 273,  1010 => 269,  1008 => 268,  1005 => 267,  1000 => 263,  978 => 258,  975 => 257,  972 => 256,  969 => 255,  966 => 254,  963 => 253,  960 => 252,  957 => 251,  954 => 250,  951 => 249,  948 => 248,  946 => 247,  943 => 246,  935 => 240,  932 => 239,  930 => 238,  927 => 237,  919 => 233,  916 => 232,  914 => 231,  911 => 230,  899 => 226,  896 => 225,  893 => 224,  890 => 223,  888 => 222,  885 => 221,  877 => 217,  874 => 216,  872 => 215,  869 => 214,  861 => 210,  858 => 209,  856 => 208,  853 => 20,  845 => 203,  842 => 15,  840 => 201,  837 => 13,  829 => 196,  826 => 195,  824 => 194,  821 => 193,  813 => 189,  810 => 188,  808 => 317,  805 => 186,  797 => 182,  794 => 181,  792 => 180,  789 => 179,  781 => 175,  779 => 174,  776 => 297,  768 => 169,  765 => 168,  763 => 167,  760 => 166,  752 => 162,  749 => 161,  747 => 160,  745 => 159,  742 => 158,  735 => 153,  725 => 152,  720 => 151,  717 => 261,  711 => 148,  708 => 255,  706 => 146,  703 => 145,  695 => 139,  693 => 317,  692 => 137,  691 => 136,  690 => 135,  685 => 134,  679 => 132,  676 => 131,  674 => 303,  671 => 129,  662 => 228,  658 => 122,  654 => 121,  650 => 120,  645 => 119,  639 => 117,  636 => 116,  634 => 278,  631 => 114,  615 => 110,  613 => 265,  610 => 108,  594 => 104,  592 => 253,  589 => 102,  572 => 98,  560 => 96,  553 => 93,  551 => 223,  546 => 91,  543 => 219,  525 => 89,  523 => 212,  520 => 87,  511 => 82,  508 => 206,  505 => 80,  499 => 78,  497 => 77,  492 => 199,  489 => 75,  486 => 74,  471 => 72,  459 => 69,  456 => 68,  450 => 182,  442 => 62,  433 => 151,  428 => 59,  426 => 173,  414 => 170,  408 => 50,  405 => 49,  403 => 48,  400 => 47,  390 => 141,  388 => 42,  385 => 41,  377 => 37,  371 => 35,  366 => 33,  363 => 32,  350 => 26,  344 => 24,  342 => 23,  335 => 141,  332 => 20,  316 => 16,  313 => 15,  311 => 14,  308 => 13,  299 => 8,  293 => 128,  290 => 5,  281 => 385,  271 => 119,  266 => 363,  263 => 128,  260 => 360,  255 => 350,  253 => 98,  250 => 97,  248 => 122,  245 => 96,  240 => 108,  238 => 309,  233 => 91,  230 => 300,  227 => 103,  217 => 101,  215 => 277,  212 => 99,  210 => 85,  207 => 97,  204 => 264,  202 => 83,  197 => 93,  194 => 245,  191 => 243,  186 => 236,  184 => 85,  181 => 229,  179 => 74,  174 => 73,  161 => 199,  146 => 62,  104 => 52,  34 => 8,  152 => 76,  129 => 145,  124 => 65,  65 => 27,  20 => 2,  90 => 32,  76 => 26,  291 => 61,  288 => 125,  279 => 43,  276 => 378,  273 => 105,  262 => 28,  257 => 27,  243 => 324,  225 => 89,  222 => 294,  218 => 72,  180 => 71,  172 => 63,  170 => 60,  159 => 67,  150 => 63,  134 => 51,  81 => 34,  63 => 19,  77 => 32,  58 => 19,  59 => 30,  53 => 18,  23 => 3,  480 => 162,  474 => 191,  469 => 164,  461 => 70,  457 => 153,  453 => 151,  444 => 149,  440 => 148,  437 => 61,  435 => 176,  430 => 144,  427 => 143,  423 => 57,  413 => 146,  409 => 145,  407 => 131,  402 => 130,  398 => 129,  393 => 162,  387 => 122,  384 => 160,  381 => 120,  379 => 119,  374 => 155,  368 => 34,  365 => 151,  362 => 110,  360 => 109,  355 => 27,  341 => 105,  337 => 22,  322 => 101,  314 => 118,  312 => 98,  309 => 117,  305 => 95,  298 => 129,  294 => 90,  285 => 3,  283 => 107,  278 => 384,  268 => 130,  264 => 84,  258 => 351,  252 => 80,  247 => 78,  241 => 77,  235 => 308,  229 => 90,  224 => 71,  220 => 103,  214 => 99,  208 => 68,  169 => 207,  143 => 57,  140 => 55,  132 => 69,  128 => 67,  119 => 52,  107 => 48,  71 => 24,  177 => 65,  165 => 64,  160 => 61,  135 => 39,  126 => 66,  114 => 51,  84 => 26,  70 => 35,  67 => 19,  61 => 2,  38 => 10,  94 => 57,  89 => 34,  85 => 36,  75 => 14,  68 => 35,  56 => 29,  87 => 25,  21 => 4,  26 => 3,  93 => 35,  88 => 35,  78 => 37,  46 => 14,  27 => 4,  44 => 14,  31 => 6,  28 => 5,  201 => 94,  196 => 81,  183 => 70,  171 => 84,  166 => 206,  163 => 68,  158 => 80,  156 => 192,  151 => 185,  142 => 61,  138 => 60,  136 => 165,  121 => 47,  117 => 25,  105 => 36,  91 => 56,  62 => 33,  49 => 8,  24 => 5,  25 => 29,  19 => 2,  79 => 32,  72 => 36,  69 => 28,  47 => 15,  40 => 10,  37 => 10,  22 => 4,  246 => 80,  157 => 56,  145 => 46,  139 => 73,  131 => 157,  123 => 31,  120 => 63,  115 => 40,  111 => 56,  108 => 37,  101 => 40,  98 => 24,  96 => 37,  83 => 33,  74 => 30,  66 => 34,  55 => 15,  52 => 11,  50 => 12,  43 => 6,  41 => 25,  35 => 3,  32 => 5,  29 => 7,  209 => 98,  203 => 78,  199 => 262,  193 => 92,  189 => 91,  187 => 86,  182 => 84,  176 => 220,  173 => 74,  168 => 70,  164 => 200,  162 => 66,  154 => 64,  149 => 44,  147 => 50,  144 => 74,  141 => 41,  133 => 48,  130 => 50,  125 => 48,  122 => 64,  116 => 45,  112 => 44,  109 => 55,  106 => 42,  103 => 26,  99 => 68,  95 => 46,  92 => 36,  86 => 41,  82 => 38,  80 => 23,  73 => 30,  64 => 34,  60 => 33,  57 => 10,  54 => 22,  51 => 27,  48 => 15,  45 => 6,  42 => 12,  39 => 10,  36 => 9,  33 => 4,  30 => 3,);
    }
}
