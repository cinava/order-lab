<?php

/* OlegOrderformBundle:MultyScanOrder:show.html.twig */
class __TwigTemplate_5e912fa91f0f31fe4bf6bee6f5c85ed3 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("OlegOrderformBundle::Default/base.html.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
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

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    
<h3 class=\"text-info\">Clinical Multy Slide Scan Order </h3>

";
        // line 9
        $context["patientCount"] = 1;
        // line 10
        $context["procedureCount"] = 1;
        // line 11
        $context["accessionCount"] = 1;
        // line 12
        $context["partCount"] = 1;
        // line 13
        $context["blockCount"] = 1;
        // line 14
        $context["slideCount"] = 1;
        // line 15
        echo "
";
        // line 27
        echo "
";
        // line 28
        $context["myform"] = $this;
        // line 29
        echo "

<div id=\"patient-data\"
           data-prototype=\"
                ";
        // line 33
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "mrn")));
        echo "
                ";
        // line 34
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "name")));
        echo "
                ";
        // line 35
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "age")));
        echo "
                ";
        // line 36
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "sex")));
        echo "
                ";
        // line 37
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "dob")));
        echo "
                ";
        // line 38
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "clinicalHistory")));
        echo "
           \"
>
</div>

<div id=\"specimen-data\"
     data-prototype=\"
        ";
        // line 45
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "specimen"), "vars"), "prototype"), "proceduretype")));
        echo "
        ";
        // line 46
        echo twig_escape_filter($this->env, $context["myform"]->getfield($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "specimen"), "vars"), "prototype"), "paper")));
        echo "
     \"
>
</div>

";
        // line 52
        echo         $this->env->getExtension('form')->renderer->renderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'form_start');
        echo "

    ";
        // line 55
        echo "    ";
        // line 56
        echo "    ";
        // line 57
        echo "
<div id=\"formpanel\" class=\"panel\">

    <div class=\"orderinfo-data\"

        ";
        // line 63
        echo "            ";
        // line 64
        echo "            ";
        // line 65
        echo "            ";
        // line 66
        echo "            ";
        // line 67
        echo "            ";
        // line 68
        echo "            ";
        // line 69
        echo "        ";
        // line 70
        echo "
        ";
        // line 72
        echo "        ";
        // line 73
        echo "        data-prototype-accession=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "specimen"), "vars"), "prototype"), "children"), "accession"), "vars"), "prototype"), 'widget'));
        echo "\"
        data-prototype-part=\"";
        // line 74
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "children"), "specimen", array(), "array"), "vars"), "prototype"), "children"), "accession", array(), "array"), "vars"), "prototype"), "children"), "part", array(), "array"), "vars"), "prototype"), 'widget'));
        echo "\"
        data-prototype-block=\"";
        // line 75
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "children"), "specimen", array(), "array"), "vars"), "prototype"), "children"), "accession", array(), "array"), "vars"), "prototype"), "children"), "part", array(), "array"), "vars"), "prototype"), "children"), "block", array(), "array"), "vars"), "prototype"), 'widget'));
        echo "\"
        data-prototype-slide=\"";
        // line 76
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"), "vars"), "prototype"), "children"), "specimen", array(), "array"), "vars"), "prototype"), "children"), "accession", array(), "array"), "vars"), "prototype"), "children"), "part", array(), "array"), "vars"), "prototype"), "children"), "block", array(), "array"), "vars"), "prototype"), "children"), "slide", array(), "array"), "vars"), "prototype"), 'widget'));
        echo "\"
    >

        ";
        // line 80
        echo "        <div id=\"formpanel_collection_patient_";
        echo twig_escape_filter($this->env, (isset($context["patientCount"]) ? $context["patientCount"] : $this->getContext($context, "patientCount")), "html", null, true);
        echo "\" class=\"panel panel-primary\">
            ";
        // line 81
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "patient"));
        foreach ($context['_seq'] as $context["_key"] => $context["patient"]) {
            // line 82
            echo "            <div class=\"patient-data\">
                <div class=\"panel-heading\">
                    Patient ";
            // line 84
            echo twig_escape_filter($this->env, (isset($context["patientCount"]) ? $context["patientCount"] : $this->getContext($context, "patientCount")), "html", null, true);
            echo "
                    <button id=\"form_body_btn_patient_";
            // line 85
            echo twig_escape_filter($this->env, (isset($context["patientCount"]) ? $context["patientCount"] : $this->getContext($context, "patientCount")), "html", null, true);
            echo "\" type=\"button\" class=\"btn btn_margin btn-mini\" data-toggle=\"collapse\" data-target=\"#form_body_patient_";
            echo twig_escape_filter($this->env, (isset($context["patientCount"]) ? $context["patientCount"] : $this->getContext($context, "patientCount")), "html", null, true);
            echo "\">
                        +/-
                    </button>
                </div>
                <div id=\"form_body_patient_";
            // line 89
            echo twig_escape_filter($this->env, (isset($context["patientCount"]) ? $context["patientCount"] : $this->getContext($context, "patientCount")), "html", null, true);
            echo "\" class=\"panel-body collapse in\">

                    ";
            // line 91
            echo $context["myform"]->getfield($this->getAttribute((isset($context["patient"]) ? $context["patient"] : $this->getContext($context, "patient")), "mrn"));
            echo "
                    ";
            // line 92
            echo $context["myform"]->getfield($this->getAttribute((isset($context["patient"]) ? $context["patient"] : $this->getContext($context, "patient")), "name"));
            echo "
                    ";
            // line 93
            echo $context["myform"]->getfield($this->getAttribute((isset($context["patient"]) ? $context["patient"] : $this->getContext($context, "patient")), "age"));
            echo "
                    ";
            // line 94
            echo $context["myform"]->getfield($this->getAttribute((isset($context["patient"]) ? $context["patient"] : $this->getContext($context, "patient")), "sex"));
            echo "
                    ";
            // line 95
            echo $context["myform"]->getfield($this->getAttribute((isset($context["patient"]) ? $context["patient"] : $this->getContext($context, "patient")), "dob"));
            echo "
                    ";
            // line 96
            echo $context["myform"]->getfield($this->getAttribute((isset($context["patient"]) ? $context["patient"] : $this->getContext($context, "patient")), "clinicalHistory"));
            echo "

                    ";
            // line 99
            echo "                    <div id=\"formpanel_collection_procedure_";
            echo twig_escape_filter($this->env, (isset($context["procedureCount"]) ? $context["procedureCount"] : $this->getContext($context, "procedureCount")), "html", null, true);
            echo "\" class=\"panel panel-primary\">
                    ";
            // line 100
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["patient"]) ? $context["patient"] : $this->getContext($context, "patient")), "specimen"));
            foreach ($context['_seq'] as $context["_key"] => $context["specimen"]) {
                // line 101
                echo "                        <div class=\"procedure-data\">
                            <div class=\"panel-heading\">
                                Procedure ";
                // line 103
                echo twig_escape_filter($this->env, (isset($context["procedureCount"]) ? $context["procedureCount"] : $this->getContext($context, "procedureCount")), "html", null, true);
                echo "
                                <button id=\"form_body_btn_procedure_";
                // line 104
                echo twig_escape_filter($this->env, (isset($context["procedureCount"]) ? $context["procedureCount"] : $this->getContext($context, "procedureCount")), "html", null, true);
                echo "\" type=\"button\" class=\"btn btn_margin btn-mini\" data-toggle=\"collapse\" data-target=\"#form_body_procedure_";
                echo twig_escape_filter($this->env, (isset($context["procedureCount"]) ? $context["procedureCount"] : $this->getContext($context, "procedureCount")), "html", null, true);
                echo "\">
                                    +/-
                                </button>
                            </div>
                            <div id=\"form_body_procedure_";
                // line 108
                echo twig_escape_filter($this->env, (isset($context["procedureCount"]) ? $context["procedureCount"] : $this->getContext($context, "procedureCount")), "html", null, true);
                echo "\" class=\"panel-body collapse in\">
                                ";
                // line 110
                echo "                                ";
                echo $context["myform"]->getfield($this->getAttribute((isset($context["specimen"]) ? $context["specimen"] : $this->getContext($context, "specimen")), "proceduretype"));
                echo "
                                ";
                // line 111
                echo $context["myform"]->getfield($this->getAttribute((isset($context["specimen"]) ? $context["specimen"] : $this->getContext($context, "specimen")), "paper"));
                echo "

                                ";
                // line 114
                echo "                                <div id=\"formpanel_collection_accession_";
                echo twig_escape_filter($this->env, (isset($context["accessionCount"]) ? $context["accessionCount"] : $this->getContext($context, "accessionCount")), "html", null, true);
                echo "\" class=\"panel panel-primary\">
                                    ";
                // line 115
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["specimen"]) ? $context["specimen"] : $this->getContext($context, "specimen")), "accession"));
                foreach ($context['_seq'] as $context["_key"] => $context["accession"]) {
                    // line 116
                    echo "                                        <div class=\"specimen-data\">
                                            <div class=\"panel-heading\">
                                                Accession ";
                    // line 118
                    echo twig_escape_filter($this->env, (isset($context["accessionCount"]) ? $context["accessionCount"] : $this->getContext($context, "accessionCount")), "html", null, true);
                    echo "
                                                <button id=\"form_body_btn_procedure_";
                    // line 119
                    echo twig_escape_filter($this->env, (isset($context["accessionCount"]) ? $context["accessionCount"] : $this->getContext($context, "accessionCount")), "html", null, true);
                    echo "\" type=\"button\" class=\"btn btn_margin btn-mini\" data-toggle=\"collapse\" data-target=\"#form_body_accession_";
                    echo twig_escape_filter($this->env, (isset($context["accessionCount"]) ? $context["accessionCount"] : $this->getContext($context, "accessionCount")), "html", null, true);
                    echo "\">
                                                    +/-
                                                </button>
                                            </div>
                                            <div id=\"form_body_accession_";
                    // line 123
                    echo twig_escape_filter($this->env, (isset($context["accessionCount"]) ? $context["accessionCount"] : $this->getContext($context, "accessionCount")), "html", null, true);
                    echo "\" class=\"panel-body collapse in\">
                                                ";
                    // line 124
                    echo $context["myform"]->getfield($this->getAttribute((isset($context["accession"]) ? $context["accession"] : $this->getContext($context, "accession")), "accession"));
                    echo "
                                                ";
                    // line 125
                    echo $context["myform"]->getfield($this->getAttribute((isset($context["accession"]) ? $context["accession"] : $this->getContext($context, "accession")), "date"));
                    echo "

                                                ";
                    // line 128
                    echo "                                                <div id=\"formpanel_collection_part_";
                    echo twig_escape_filter($this->env, (isset($context["partCount"]) ? $context["partCount"] : $this->getContext($context, "partCount")), "html", null, true);
                    echo "\" class=\"panel panel-primary\">
                                                    ";
                    // line 129
                    $context['_parent'] = (array) $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["accession"]) ? $context["accession"] : $this->getContext($context, "accession")), "part"));
                    foreach ($context['_seq'] as $context["_key"] => $context["part"]) {
                        // line 130
                        echo "                                                        <div class=\"specimen-data\">
                                                            <div class=\"panel-heading\">
                                                                Part ";
                        // line 132
                        echo twig_escape_filter($this->env, (isset($context["partCount"]) ? $context["partCount"] : $this->getContext($context, "partCount")), "html", null, true);
                        echo "
                                                                <button id=\"form_body_btn_part_";
                        // line 133
                        echo twig_escape_filter($this->env, (isset($context["partCount"]) ? $context["partCount"] : $this->getContext($context, "partCount")), "html", null, true);
                        echo "\" type=\"button\" class=\"btn btn_margin btn-mini\" data-toggle=\"collapse\" data-target=\"#form_body_part_";
                        echo twig_escape_filter($this->env, (isset($context["partCount"]) ? $context["partCount"] : $this->getContext($context, "partCount")), "html", null, true);
                        echo "\">
                                                                    +/-
                                                                </button>
                                                            </div>
                                                            <div id=\"form_body_part_";
                        // line 137
                        echo twig_escape_filter($this->env, (isset($context["partCount"]) ? $context["partCount"] : $this->getContext($context, "partCount")), "html", null, true);
                        echo "\" class=\"panel-body collapse in\">
                                                                ";
                        // line 138
                        echo $context["myform"]->getfield($this->getAttribute((isset($context["part"]) ? $context["part"] : $this->getContext($context, "part")), "name"));
                        echo "
                                                                ";
                        // line 139
                        echo $context["myform"]->getfield($this->getAttribute((isset($context["part"]) ? $context["part"] : $this->getContext($context, "part")), "sourceOrgan"));
                        echo "
                                                                ";
                        // line 140
                        echo $context["myform"]->getfield($this->getAttribute((isset($context["part"]) ? $context["part"] : $this->getContext($context, "part")), "description"));
                        echo "
                                                                ";
                        // line 141
                        echo $context["myform"]->getfield($this->getAttribute((isset($context["part"]) ? $context["part"] : $this->getContext($context, "part")), "diagnosis"));
                        echo "
                                                                ";
                        // line 142
                        echo $context["myform"]->getfield($this->getAttribute((isset($context["part"]) ? $context["part"] : $this->getContext($context, "part")), "diffDiagnosis"));
                        echo "
                                                                ";
                        // line 143
                        echo $context["myform"]->getfield($this->getAttribute((isset($context["part"]) ? $context["part"] : $this->getContext($context, "part")), "diseaseType"));
                        echo "

                                                                ";
                        // line 146
                        echo "                                                                <div id=\"formpanel_collection_block_";
                        echo twig_escape_filter($this->env, (isset($context["blockCount"]) ? $context["blockCount"] : $this->getContext($context, "blockCount")), "html", null, true);
                        echo "\" class=\"panel panel-primary\">
                                                                    ";
                        // line 147
                        $context['_parent'] = (array) $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["part"]) ? $context["part"] : $this->getContext($context, "part")), "block"));
                        foreach ($context['_seq'] as $context["_key"] => $context["block"]) {
                            // line 148
                            echo "                                                                        <div class=\"block-data\">
                                                                            <div class=\"panel-heading\">
                                                                                Block ";
                            // line 150
                            echo twig_escape_filter($this->env, (isset($context["blockCount"]) ? $context["blockCount"] : $this->getContext($context, "blockCount")), "html", null, true);
                            echo "
                                                                                <button id=\"form_body_btn_block_";
                            // line 151
                            echo twig_escape_filter($this->env, (isset($context["blockCount"]) ? $context["blockCount"] : $this->getContext($context, "blockCount")), "html", null, true);
                            echo "\" type=\"button\" class=\"btn btn_margin btn-mini\" data-toggle=\"collapse\" data-target=\"#form_body_block_";
                            echo twig_escape_filter($this->env, (isset($context["blockCount"]) ? $context["blockCount"] : $this->getContext($context, "blockCount")), "html", null, true);
                            echo "\">
                                                                                    +/-
                                                                                </button>
                                                                            </div>
                                                                            <div id=\"form_body_block_";
                            // line 155
                            echo twig_escape_filter($this->env, (isset($context["blockCount"]) ? $context["blockCount"] : $this->getContext($context, "blockCount")), "html", null, true);
                            echo "\" class=\"panel-body collapse in\">
                                                                                ";
                            // line 156
                            echo $context["myform"]->getfield($this->getAttribute((isset($context["block"]) ? $context["block"] : $this->getContext($context, "block")), "name"));
                            echo "


                                                                                ";
                            // line 160
                            echo "                                                                                <div id=\"formpanel_collection_slide_";
                            echo twig_escape_filter($this->env, (isset($context["slideCount"]) ? $context["slideCount"] : $this->getContext($context, "slideCount")), "html", null, true);
                            echo "\" class=\"panel panel-primary\">
                                                                                    ";
                            // line 161
                            $context['_parent'] = (array) $context;
                            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["block"]) ? $context["block"] : $this->getContext($context, "block")), "slide"));
                            foreach ($context['_seq'] as $context["_key"] => $context["slide"]) {
                                // line 162
                                echo "                                                                                        <div class=\"slide-data\">
                                                                                            <div class=\"panel-heading\">
                                                                                                Slide ";
                                // line 164
                                echo twig_escape_filter($this->env, (isset($context["slideCount"]) ? $context["slideCount"] : $this->getContext($context, "slideCount")), "html", null, true);
                                echo "
                                                                                                <button id=\"form_body_btn_slide_";
                                // line 165
                                echo twig_escape_filter($this->env, (isset($context["slideCount"]) ? $context["slideCount"] : $this->getContext($context, "slideCount")), "html", null, true);
                                echo "\" type=\"button\" class=\"btn btn_margin btn-mini\" data-toggle=\"collapse\" data-target=\"#form_body_slide_";
                                echo twig_escape_filter($this->env, (isset($context["slideCount"]) ? $context["slideCount"] : $this->getContext($context, "slideCount")), "html", null, true);
                                echo "\">
                                                                                                    +/-
                                                                                                </button>
                                                                                            </div>
                                                                                            <div id=\"form_body_slide_";
                                // line 169
                                echo twig_escape_filter($this->env, (isset($context["slideCount"]) ? $context["slideCount"] : $this->getContext($context, "slideCount")), "html", null, true);
                                echo "\" class=\"panel-body collapse in\">
                                                                                                ";
                                // line 170
                                echo $context["myform"]->getfield($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "diagnosis"));
                                echo "
                                                                                                ";
                                // line 171
                                echo $context["myform"]->getfield($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "microscopicdescr"));
                                echo "
                                                                                                ";
                                // line 172
                                echo $context["myform"]->getfield($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "specialstain"));
                                echo "
                                                                                                ";
                                // line 173
                                echo $context["myform"]->getfield($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "relevantscan"));
                                echo "

                                                                                                ";
                                // line 175
                                echo $context["myform"]->getfield($this->getAttribute($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "stain"), "name"));
                                echo "
                                                                                                ";
                                // line 176
                                echo $context["myform"]->getfield($this->getAttribute($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "scan"), "mag"));
                                echo "
                                                                                                ";
                                // line 177
                                echo $context["myform"]->getfield($this->getAttribute($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "scan"), "scanregion"));
                                echo "
                                                                                                ";
                                // line 178
                                echo $context["myform"]->getfield($this->getAttribute($this->getAttribute((isset($context["slide"]) ? $context["slide"] : $this->getContext($context, "slide")), "scan"), "note"));
                                echo "

                                                                                            </div>
                                                                                        </div>
                                                                                        ";
                                // line 182
                                $context["slideCount"] = ((isset($context["slideCount"]) ? $context["slideCount"] : $this->getContext($context, "slideCount")) + 1);
                                // line 183
                                echo "                                                                                    ";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['slide'], $context['_parent'], $context['loop']);
                            $context = array_intersect_key($context, $_parent) + $_parent;
                            // line 184
                            echo "                                                                                </div> ";
                            // line 185
                            echo "

                                                                            </div>
                                                                        </div>
                                                                        ";
                            // line 189
                            $context["blockCount"] = ((isset($context["blockCount"]) ? $context["blockCount"] : $this->getContext($context, "blockCount")) + 1);
                            // line 190
                            echo "                                                                    ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['block'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 191
                        echo "                                                                </div> ";
                        // line 192
                        echo "

                                                            </div>
                                                        </div>
                                                        ";
                        // line 196
                        $context["partCount"] = ((isset($context["partCount"]) ? $context["partCount"] : $this->getContext($context, "partCount")) + 1);
                        // line 197
                        echo "                                                    ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['part'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 198
                    echo "                                                </div> ";
                    // line 199
                    echo "

                                            </div>
                                        </div>
                                        ";
                    // line 203
                    $context["accessionCount"] = ((isset($context["accessionCount"]) ? $context["accessionCount"] : $this->getContext($context, "accessionCount")) + 1);
                    // line 204
                    echo "                                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['accession'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 205
                echo "                                </div> ";
                // line 206
                echo "

                            </div>
                        </div>
                        ";
                // line 210
                $context["procedureCount"] = ((isset($context["procedureCount"]) ? $context["procedureCount"] : $this->getContext($context, "procedureCount")) + 1);
                // line 211
                echo "                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['specimen'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            echo " ";
            // line 212
            echo "                    <button id=\"form_add_btn\" type=\"button\" class=\"btn btn_margin\" onclick=\"addSameForm('procedure', ";
            echo twig_escape_filter($this->env, (isset($context["procedureCount"]) ? $context["procedureCount"] : $this->getContext($context, "procedureCount")), "html", null, true);
            echo ")\">Add Procedure</button>
                </div>";
            // line 214
            echo "
                </div>";
            // line 216
            echo "            </div>";
            // line 217
            echo "                ";
            $context["patientCount"] = ((isset($context["patientCount"]) ? $context["patientCount"] : $this->getContext($context, "patientCount")) + 1);
            // line 218
            echo "            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['patient'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        echo " ";
        // line 219
        echo "        <button id=\"form_add_btn\" type=\"button\" class=\"btn btn_margin\" onclick=\"addSameForm('patient', ";
        echo twig_escape_filter($this->env, (isset($context["patientCount"]) ? $context["patientCount"] : $this->getContext($context, "patientCount")), "html", null, true);
        echo ")\">Add Patient</button>
        </div>";
        // line 221
        echo "
    </div>";
        // line 223
        echo "

    ";
        // line 226
        echo "        ";
        // line 227
        echo "    ";
        // line 228
        echo "
";
        // line 231
        echo "
    <button id=\"next_button\" type=\"button\" class=\"btn\" 
            data-toggle=\"collapse\" data-target=\"#orderinfo_param\">Next
    </button>


    <div id=\"orderinfo_param\" class=\"collapse\">
        <div class=\"row-fluid\">
        <div class=\"span12\">
        <h4>Scan Order Info</h4>   
        </div>
        </div>
        <div class=\"row-fluid\">
        <div class=\"span6\" align=\"right\">
        ";
        // line 245
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "provider"), 'label');
        echo "
        </div>
        <div class=\"span6\" align=\"left\">
        ";
        // line 248
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "provider"), 'widget');
        echo " 
        </div>
        </div>
        <div class=\"row-fluid\">
        <div class=\"span6\" align=\"right\">
        ";
        // line 253
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "pathologyService"), 'label');
        echo "
        </div>
        <div class=\"span6\" align=\"left\">
        ";
        // line 256
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "pathologyService"), 'widget');
        echo "
        </div>
        </div>

        <div class=\"row-fluid\">
        <div class=\"span6\" align=\"right\">
        ";
        // line 262
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "priority"), 'label');
        echo "
        </div>
        <div class=\"span6\" align=\"left\">
        ";
        // line 265
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "priority"), 'widget');
        echo "
        </div>
        </div>
";
        // line 269
        echo "        <div id=\"priority_option\" class=\"collapse\"> 
";
        // line 271
        echo "        <div class=\"well\">

            <div class=\"row-fluid\">              
            <div class=\"span6\" align=\"right\">              
            ";
        // line 275
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "scandeadline"), 'label');
        echo "
            </div>            
            <div class=\"span6\" align=\"left\">
            ";
        // line 278
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "scandeadline"), 'widget');
        echo " 
            </div>   
            </div>    

            <div class=\"row-fluid\">              
            <div class=\"span6\" align=\"right\">
            ";
        // line 284
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "returnoption"), 'label');
        echo "
            </div>            
            <div class=\"span6\" align=\"left\">
            ";
        // line 287
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "returnoption"), 'widget');
        echo " 
            </div>   
            </div>    
        </div>    
        </div>  

        <div class=\"row-fluid\">
        <div class=\"span6\" align=\"right\">
        ";
        // line 295
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "slideDelivery"), 'label');
        echo "
        </div>
        <div class=\"span6\" align=\"left\">
        ";
        // line 298
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "slideDelivery"), 'widget');
        echo "
        </div>
        </div>
        <div class=\"row-fluid\">
        <div class=\"span6\" align=\"right\">
        ";
        // line 303
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "returnSlide"), 'label');
        echo "
        </div>
        <div class=\"span6\" align=\"left\">
        ";
        // line 306
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "returnSlide"), 'widget');
        echo "
        </div>
        </div>
        <div class=\"row-fluid\">
        <div class=\"span12\">
            <button class=\"btn_margin_top btn btn-primary btn-success\" type=\"submit\">Submit</button>        
        </div>
        </div> 
    </div>
   
     ";
        // line 317
        echo "
</div>
    
";
        // line 320
        echo         $this->env->getExtension('form')->renderer->renderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'form_end');
        echo "
";
        // line 322
        echo "    
";
    }

    // line 17
    public function getfield($_field = null)
    {
        $context = $this->env->mergeGlobals(array(
            "field" => $_field,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 18
            echo "    <div class=\"row-fluid\">
        <div class=\"span6\" align=\"right\">
            ";
            // line 20
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["field"]) ? $context["field"] : $this->getContext($context, "field")), 'label');
            echo "
        </div>
        <div class=\"span6\" align=\"left\">
            ";
            // line 23
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["field"]) ? $context["field"] : $this->getContext($context, "field")), 'widget');
            echo "
        </div>
    </div>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "OlegOrderformBundle:MultyScanOrder:show.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  728 => 23,  722 => 20,  718 => 18,  707 => 17,  698 => 320,  680 => 306,  666 => 298,  643 => 284,  628 => 275,  622 => 271,  619 => 269,  607 => 262,  598 => 256,  584 => 248,  578 => 245,  559 => 228,  557 => 227,  555 => 226,  548 => 221,  536 => 218,  533 => 217,  531 => 216,  528 => 214,  516 => 211,  514 => 210,  500 => 204,  490 => 198,  484 => 197,  482 => 196,  476 => 192,  468 => 190,  466 => 189,  460 => 185,  458 => 184,  452 => 183,  443 => 178,  439 => 177,  431 => 175,  422 => 172,  418 => 171,  410 => 169,  401 => 165,  397 => 164,  389 => 161,  357 => 148,  353 => 147,  343 => 143,  339 => 142,  319 => 137,  310 => 133,  302 => 130,  284 => 124,  280 => 123,  254 => 114,  249 => 111,  244 => 110,  231 => 104,  223 => 101,  219 => 100,  205 => 95,  175 => 85,  167 => 82,  148 => 75,  137 => 72,  848 => 18,  834 => 12,  823 => 11,  817 => 323,  814 => 321,  803 => 316,  790 => 305,  784 => 302,  770 => 294,  759 => 286,  753 => 283,  744 => 277,  738 => 274,  732 => 270,  729 => 268,  723 => 264,  702 => 322,  694 => 247,  688 => 244,  672 => 230,  665 => 229,  660 => 295,  656 => 224,  649 => 287,  646 => 222,  641 => 218,  635 => 217,  632 => 216,  627 => 212,  621 => 211,  618 => 210,  612 => 205,  606 => 204,  604 => 203,  602 => 202,  597 => 199,  591 => 198,  588 => 197,  583 => 194,  579 => 193,  575 => 192,  570 => 191,  566 => 189,  562 => 231,  558 => 187,  554 => 186,  550 => 185,  547 => 184,  529 => 182,  527 => 181,  515 => 177,  509 => 175,  506 => 205,  501 => 173,  498 => 203,  488 => 167,  485 => 166,  467 => 163,  463 => 162,  455 => 159,  449 => 157,  446 => 156,  441 => 155,  438 => 154,  429 => 150,  425 => 149,  421 => 148,  417 => 147,  406 => 144,  392 => 142,  386 => 140,  378 => 156,  372 => 135,  369 => 134,  364 => 133,  361 => 150,  356 => 129,  352 => 128,  348 => 146,  345 => 126,  333 => 124,  331 => 140,  327 => 139,  323 => 138,  317 => 119,  306 => 132,  301 => 113,  297 => 112,  267 => 118,  259 => 115,  242 => 95,  237 => 92,  221 => 88,  213 => 86,  200 => 82,  190 => 78,  118 => 52,  153 => 62,  102 => 34,  100 => 47,  113 => 57,  110 => 50,  97 => 37,  1357 => 388,  1348 => 387,  1346 => 386,  1343 => 385,  1327 => 381,  1320 => 380,  1318 => 379,  1315 => 378,  1292 => 374,  1267 => 373,  1265 => 372,  1262 => 371,  1250 => 366,  1245 => 365,  1243 => 364,  1240 => 363,  1231 => 357,  1225 => 355,  1222 => 354,  1217 => 353,  1215 => 352,  1212 => 351,  1205 => 346,  1196 => 344,  1192 => 343,  1189 => 342,  1186 => 341,  1184 => 340,  1181 => 339,  1173 => 335,  1171 => 334,  1168 => 333,  1162 => 329,  1156 => 327,  1153 => 326,  1151 => 325,  1148 => 324,  1139 => 319,  1137 => 318,  1114 => 317,  1111 => 316,  1108 => 315,  1105 => 314,  1102 => 313,  1099 => 312,  1096 => 311,  1094 => 310,  1091 => 309,  1084 => 305,  1080 => 304,  1075 => 303,  1073 => 302,  1070 => 301,  1063 => 296,  1060 => 295,  1052 => 290,  1049 => 289,  1047 => 288,  1044 => 287,  1036 => 282,  1032 => 281,  1028 => 280,  1025 => 279,  1023 => 278,  1020 => 277,  1012 => 273,  1010 => 269,  1008 => 268,  1005 => 267,  1000 => 263,  978 => 258,  975 => 257,  972 => 256,  969 => 255,  966 => 254,  963 => 253,  960 => 252,  957 => 251,  954 => 250,  951 => 249,  948 => 248,  946 => 247,  943 => 246,  935 => 240,  932 => 239,  930 => 238,  927 => 237,  919 => 233,  916 => 232,  914 => 231,  911 => 230,  899 => 226,  896 => 225,  893 => 224,  890 => 223,  888 => 222,  885 => 221,  877 => 217,  874 => 216,  872 => 215,  869 => 214,  861 => 210,  858 => 209,  856 => 208,  853 => 20,  845 => 203,  842 => 15,  840 => 201,  837 => 13,  829 => 196,  826 => 195,  824 => 194,  821 => 193,  813 => 189,  810 => 188,  808 => 317,  805 => 186,  797 => 182,  794 => 181,  792 => 180,  789 => 179,  781 => 175,  779 => 174,  776 => 297,  768 => 169,  765 => 168,  763 => 167,  760 => 166,  752 => 162,  749 => 161,  747 => 160,  745 => 159,  742 => 158,  735 => 153,  725 => 152,  720 => 151,  717 => 261,  711 => 148,  708 => 255,  706 => 146,  703 => 145,  695 => 139,  693 => 317,  692 => 137,  691 => 136,  690 => 135,  685 => 134,  679 => 132,  676 => 131,  674 => 303,  671 => 129,  662 => 228,  658 => 122,  654 => 121,  650 => 120,  645 => 119,  639 => 117,  636 => 116,  634 => 278,  631 => 114,  615 => 110,  613 => 265,  610 => 108,  594 => 104,  592 => 253,  589 => 102,  572 => 98,  560 => 96,  553 => 93,  551 => 223,  546 => 91,  543 => 219,  525 => 89,  523 => 212,  520 => 87,  511 => 82,  508 => 206,  505 => 80,  499 => 78,  497 => 77,  492 => 199,  489 => 75,  486 => 74,  471 => 72,  459 => 69,  456 => 68,  450 => 182,  442 => 62,  433 => 151,  428 => 59,  426 => 173,  414 => 170,  408 => 50,  405 => 49,  403 => 48,  400 => 47,  390 => 141,  388 => 42,  385 => 41,  377 => 37,  371 => 35,  366 => 33,  363 => 32,  350 => 26,  344 => 24,  342 => 23,  335 => 141,  332 => 20,  316 => 16,  313 => 15,  311 => 14,  308 => 13,  299 => 8,  293 => 128,  290 => 5,  281 => 385,  271 => 119,  266 => 363,  263 => 116,  260 => 360,  255 => 350,  253 => 98,  250 => 97,  248 => 333,  245 => 96,  240 => 108,  238 => 309,  233 => 91,  230 => 300,  227 => 103,  217 => 87,  215 => 277,  212 => 276,  210 => 85,  207 => 266,  204 => 264,  202 => 83,  197 => 93,  194 => 245,  191 => 243,  186 => 236,  184 => 89,  181 => 229,  179 => 74,  174 => 73,  161 => 199,  146 => 62,  104 => 52,  34 => 6,  152 => 76,  129 => 145,  124 => 65,  65 => 26,  20 => 2,  90 => 32,  76 => 37,  291 => 61,  288 => 125,  279 => 43,  276 => 378,  273 => 105,  262 => 28,  257 => 27,  243 => 324,  225 => 89,  222 => 294,  218 => 72,  180 => 71,  172 => 63,  170 => 60,  159 => 193,  150 => 63,  134 => 70,  81 => 34,  63 => 25,  77 => 32,  58 => 19,  59 => 30,  53 => 18,  23 => 3,  480 => 162,  474 => 191,  469 => 164,  461 => 70,  457 => 153,  453 => 151,  444 => 149,  440 => 148,  437 => 61,  435 => 176,  430 => 144,  427 => 143,  423 => 57,  413 => 146,  409 => 145,  407 => 131,  402 => 130,  398 => 129,  393 => 162,  387 => 122,  384 => 160,  381 => 120,  379 => 119,  374 => 155,  368 => 34,  365 => 151,  362 => 110,  360 => 109,  355 => 27,  341 => 105,  337 => 22,  322 => 101,  314 => 118,  312 => 98,  309 => 117,  305 => 95,  298 => 129,  294 => 90,  285 => 3,  283 => 107,  278 => 384,  268 => 370,  264 => 84,  258 => 351,  252 => 80,  247 => 78,  241 => 77,  235 => 308,  229 => 90,  224 => 71,  220 => 287,  214 => 99,  208 => 68,  169 => 207,  143 => 58,  140 => 55,  132 => 69,  128 => 67,  119 => 52,  107 => 48,  71 => 20,  177 => 65,  165 => 64,  160 => 61,  135 => 39,  126 => 66,  114 => 51,  84 => 26,  70 => 35,  67 => 19,  61 => 2,  38 => 10,  94 => 57,  89 => 34,  85 => 36,  75 => 14,  68 => 35,  56 => 29,  87 => 25,  21 => 4,  26 => 3,  93 => 35,  88 => 37,  78 => 37,  46 => 14,  27 => 4,  44 => 13,  31 => 6,  28 => 5,  201 => 94,  196 => 81,  183 => 70,  171 => 84,  166 => 206,  163 => 81,  158 => 80,  156 => 192,  151 => 185,  142 => 61,  138 => 60,  136 => 165,  121 => 128,  117 => 25,  105 => 36,  91 => 56,  62 => 33,  49 => 8,  24 => 5,  25 => 29,  19 => 2,  79 => 32,  72 => 36,  69 => 28,  47 => 15,  40 => 11,  37 => 10,  22 => 4,  246 => 80,  157 => 56,  145 => 46,  139 => 73,  131 => 157,  123 => 31,  120 => 63,  115 => 40,  111 => 56,  108 => 37,  101 => 32,  98 => 24,  96 => 46,  83 => 22,  74 => 36,  66 => 34,  55 => 15,  52 => 18,  50 => 12,  43 => 6,  41 => 25,  35 => 3,  32 => 5,  29 => 5,  209 => 96,  203 => 78,  199 => 262,  193 => 92,  189 => 91,  187 => 84,  182 => 75,  176 => 220,  173 => 74,  168 => 70,  164 => 200,  162 => 66,  154 => 64,  149 => 44,  147 => 50,  144 => 74,  141 => 41,  133 => 48,  130 => 68,  125 => 52,  122 => 64,  116 => 51,  112 => 43,  109 => 55,  106 => 49,  103 => 26,  99 => 68,  95 => 46,  92 => 45,  86 => 41,  82 => 38,  80 => 23,  73 => 30,  64 => 34,  60 => 33,  57 => 10,  54 => 28,  51 => 27,  48 => 15,  45 => 6,  42 => 12,  39 => 10,  36 => 9,  33 => 4,  30 => 3,);
    }
}
